<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Shield\Entities\User;
use CodeIgniter\Shield\Models\UserModel;

class UserSeeder extends Seeder
{
    public function run()
    {
        $user = new UserModel;

        $faker = \Faker\Factory::create();

        $data = [
            /* array(
                "username" => "yskim",
                "email" => "ryan1219@carelabs.co.kr"
            ),
            array(
                "username" => "hsm",
                "email" => "hsm0301@carelabs.co.kr"
            ),
            array(
                "username" => "lek",
                "email" => "eunkyoung@carelabs.co.kr"
            ),
            array(
                "username" => "kuhellian",
                "email" => "kimcg@carelabs.co.kr"
            ),
            array(
                "username" => "vcvxvz",
                "email" => "nylee@carelabs.co.kr"
            ),
            array(
                "username" => "dudtjr9169",
                "email" => "kys9169@carelabs.co.kr"
            ),
            array(
                "username" => "hermesheo",
                "email" => "min.heo@carelabs.co.kr"
            ), */
            /* array(
                "username" => "jms",
                "email" => "jms@carelabs.co.kr"
            ), */
            /* array(
                "username" => "kumssac",
                "email" => "kumssac@carelabs.co.kr"
            ),
            array(
                "username" => "hjs",
                "email" => "spacejin01@carelabs.co.kr"
            ),
            array(
                "username" => "yjj",
                "email" => "yjj@carelabs.co.kr"
            ), */
            /* array(
                "username" => "jaybe",
                "email" => "jaybe@carelabs.co.kr"
            ), */
            /* array(
                "username" => "dltjwls247",
                "email" => "seo@carelabs.co.kr"
            ),
            array(
                "username" => "designloop",
                "email" => "dloop@carelabs.co.kr"
            ),
            array(
                "username" => "gblagdog",
                "email" => "gblagdog@carelabs.co.kr"
            ),
            array(
                "username" => "sgksks",
                "email" => "sgksks@carelabs.co.kr"
            ),
            array(
                "username" => "hahazz",
                "email" => "hahazz@carelabs.co.kr"
            ),
            array(
                "username" => "darkyong",
                "email" => "darkyong@carelabs.co.kr"
            ),
            array(
                "username" => "chosk",
                "email" => "chosk@carelabs.co.kr"
            ),
            array(
                "username" => "zfkltm320",
                "email" => "zfkltm320@carelabs.co.kr"
            ),
            array(
                "username" => "jinseon5415",
                "email" => "jinseon5415@carelabs.co.kr"
            ),
            array(
                "username" => "hamstouch",
                "email" => "hamstouch@carelabs.co.kr"
            ),
            array(
                "username" => "gschoi",
                "email" => "gs.choi@carelabs.co.kr"
            ),
            array(
                "username" => "hokeg1",
                "email" => "hokeg1@carelabs.co.kr"
            ),
            array(
                "username" => "kimhr425",
                "email" => "kimhr425@carelabs.co.kr"
            ),
            array(
                "username" => "gksquf123",
                "email" => "gksquf123@carelabs.co.kr"
            ),
            array(
                "username" => "hyeji0125",
                "email" => "hyeji0125@carelabs.co.kr"
            ),
            array(
                "username" => "zxwcll",
                "email" => "zxwcll@carelabs.co.kr"
            ),
            array(
                "username" => "rong2",
                "email" => "rong2@carelabs.co.kr"
            ),
            array(
                "username" => "oklhw38",
                "email" => "oklhw38@carelabs.co.kr"
            ),
            array(
                "username" => "loveholic",
                "email" => "loveholic@carelabs.co.kr"
            ),
            array(
                "username" => "future",
                "email" => "future@carelabs.co.kr"
            ), */
            /* array(
                "username" => "tjwlgml47",
                "email" => "tjwlgml47@carelabs.co.kr"
            ), */
            /* array(
                "username" => "azure871129",
                "email" => "yjm@carelabs.co.kr"
            ),
            array(
                "username" => "osd92",
                "email" => "osd92@carelabs.co.kr"
            ),
            array(
                "username" => "clapone",
                "email" => "clapone@carelabs.co.kr"
            ),
            array(
                "username" => "rhea7904",
                "email" => "rhea7904@carelabs.co.kr"
            ),
            array(
                "username" => "dkstjrals",
                "email" => "dkstjrals@carelabs.co.kr"
            ), */
            /* array(
                "username" => "ms1114",
                "email" => "ms1114@carelabs.co.kr"
            ), */
            /* array(
                "username" => "khjkhj1202",
                "email" => "khjkhj1202@carelabs.co.kr"
            ),
            array(
                "username" => "ohyeonjae",
                "email" => "ohyeonjae@carelabs.co.kr"
            ),
            array(
                "username" => "chanyeong",
                "email" => "chanyeong@carelabs.co.kr"
            ), */
            array(
                "username" => "pjh8778",
                "nickname" => "박종호",
                "email" => "pjh8778@carelabs.co.kr",
            ),
            array(
                "username" => "sungjin",
                "nickname" => "박성진",
                "email" => "sunshine_psj@carelabs.co.kr"
            ),
        ];

        for ($i = 0; $i < count($data); $i++) {
            $data[$i]['password'] = '1234';
            $data[$i]['password_confirm'] = '1234';
            $userModel = model(UserModel::class);
            $user = new User();
            $user->fill($data[$i]);
            $userModel->save($user);
            $user = $userModel->findById($userModel->getInsertID());

            $user->addGroup('guest');
            $user->activate();
        }
    }
}
