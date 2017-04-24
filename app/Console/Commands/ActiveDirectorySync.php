<?php

namespace Proto\Console\Commands;

use Adldap\Adldap;
use Adldap\Connections\Provider;
use Adldap\Objects\AccountControl;

use Illuminate\Console\Command;
use Proto\Models\User;
use Proto\Models\Committee;

use Proto\Http\Controllers\SlackController;

use Adldap\Exceptions\Auth\BindException;

/**
 * TODO
 * Autorelate permissions to roles.
 */
class ActiveDirectorySync extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'proto:adsync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manages e-mail accounts and lists via DirectAdmin.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        try {
            $ad = new Adldap();
            $provider = new Provider(config('adldap.proto'));
            $ad->addProvider('proto', $provider);
            $ad->connect('proto');

            $this->info("Connected to LDAP server.");

            $this->info("Synchronizing users to LDAP.");
            $this->syncUsers($provider);

            $this->info("Synchronizing committees to LDAP.");
            $this->syncCommittees($provider);

            $this->info("Synchronizing committees members to LDAP.");
            $this->syncCommitteeMembers($provider);

            $this->info("Done!");
        } catch (BindException $e) {
            $this->error('Could not bind with LDAP server!');
            SlackController::sendNotification('[console *proto:adsync*] Could not bind with LDAP Server.');
        }
    }

    private function syncUsers($provider)
    {

        $activeIds = [];

        $this->info("Make sure all users exist in LDAP.");

        foreach (User::all() as $user) {

            $activeIds[] = $user->id;
            $ldapuser = $provider->search()->where('objectClass', 'user')->where('description', $user->id)->first();

            $username = ($user->member ? $user->member->proto_username : "user-" . $user->id);

            if ($ldapuser == null) {
                $this->info('Creating LDAP user for ' . $user->name . '.');
                $ldapuser = $provider->make()->user();
                $ldapuser->cn = $username;
                $ldapuser->description = $user->id;
                $ldapuser->save();
            }

            $ldapuser->move('cn=' . $username, 'OU=Members,OU=Proto,DC=ad,DC=saproto,DC=nl');

            $ldapuser->displayName = trim($user->name);
            $ldapuser->givenName = trim($user->calling_name);

            $ldapuser->mail = $user->email;
            $ldapuser->wWWHomePage = $user->website;

            if ($user->address) {

                $ldapuser->l = $user->address->city;
                $ldapuser->postalCode = $user->address->zipcode;
                $ldapuser->streetAddress = $user->address->street . " " . $user->address->number;
                $ldapuser->preferredLanguage = $user->address->country;

            } else {

                $ldapuser->l = null;
                $ldapuser->postalCode = null;
                $ldapuser->streetAddress = null;
                $ldapuser->preferredLanguage = null;

            }

            $ldapuser->telephoneNumber = $user->phone;

            $ldapuser->setAttribute('sAMAccountName', $username);
            $ldapuser->setUserPrincipalName($username . config('adldap.proto')['account_suffix']);

            if (!$user->member) {
                $ldapuser->setUserAccountControl(AccountControl::ACCOUNTDISABLE);
            }

            $ldapuser->save();

        }

        $this->info("Removing obsolete users from LDAP.");

        $users = $provider->search()->users()->get();

        foreach ($users as $user) {
            if (!$user->description[0] || !in_array($user->description[0], $activeIds)) {
                $this->info("Deleting LDAP user " . $user->description[0] . ".");
                $user->delete();
            }
        }

    }

    private function syncCommittees($provider)
    {

        $activeIds = [];

        $this->info("Make sure all committees exist in LDAP.");

        foreach (Committee::all() as $committee) {

            $activeIds[] = $committee->id;
            $group = $provider->search()->where('objectClass', 'group')->where('description', $committee->id)->first();

            if ($group == null) {
                $this->info('Creating LDAP group for ' . $committee->name . '.');
                $group = $provider->make()->group();
                $group->cn = trim($committee->name);
                $group->description = $committee->id;
                $group->save();
            }

            $group->move('cn=' . trim($committee->name), 'OU=Committees,OU=Proto,DC=ad,DC=saproto,DC=nl');
            $group->displayName = trim($committee->name);
            $group->description = $committee->id;
            $group->mail = $committee->slug . '@' . config('proto.emaildomain');
            $group->url = route("committee::show", ['id' => $committee->id]);

            $group->setAttribute('sAMAccountName', $committee->slug);

            $group->save();

        }

        $this->info("Removing obsolete committees from LDAP.");

        $committees = $provider->search()->groups()->get();

        foreach ($committees as $group) {
            if (!$group->description[0] || !in_array($group->description[0], $activeIds)) {
                $this->info("Deleting LDAP group " . $group->description[0] . ".");
                $group->delete();
            }
        }

    }

    private function syncCommitteeMembers($provider)
    {

        $groups = $provider->search()->groups()->get();

        $user2ldap = [];

        foreach ($groups as $group) {

            $this->info('Setting members for ' . $group->name[0] . '.');

            $committee = Committee::findOrFail($group->description[0]);

            $newmembers = [];

            foreach ($committee->users as $user) {

                if (!array_key_exists($user->id, $user2ldap)) {

                    $ldapuser = $provider->search()->where('objectClass', 'user')->where('description', $user->id)->first();
                    if ($ldapuser !== null) {
                        $user2ldap[$user->id] = $ldapuser;
                    } else {
                        $this->error("No LDAP user found for " . $user->name . ".");
                        continue;
                    }

                }

                if (!in_array($user2ldap[$user->id]->dn, $newmembers)) {
                    $newmembers[] = $user2ldap[$user->id]->dn;
                }

            }

            $group->setMembers($newmembers);
            $group->save();

        }

    }

}
