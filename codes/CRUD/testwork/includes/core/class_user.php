<?php

class User {

    // GENERAL

    public static function user_info($d) {
        // vars
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $phone = isset($d['phone']) ? preg_replace('~\D+~', '', $d['phone']) : 0;
        // where
        if ($user_id) $where = "user_id='".$user_id."'";
        else if ($phone) $where = "phone='".$phone."'";
        else return [];
        // info
        $q = DB::query("SELECT user_id, phone, access FROM users WHERE ".$where." LIMIT 1;") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'access' => (int) $row['access']
            ];
        } else {
            return [
                'id' => 0,
                'access' => 0
            ];
        }
    }

    public static function user_edit_info($user_id) {
//        "SELECT users.user_id, first_name, last_name, phone, email, GROUP_CONCAT(plots.number SEPARATOR ', ') as plots
//            FROM users
//            LEFT JOIN plot_user ON users.user_id = plot_user.user_id
//            LEFT JOIN plots ON plot_user.plot_id = plots.plot_id
//            ".$where."  GROUP BY users.user_id";
//        $q = DB::query("SELECT user_id, first_name, last_name, phone, email
//            FROM users WHERE user_id='".$user_id."' LIMIT 1;") or die (DB::error());

            $q = DB::query("SELECT users.user_id, first_name, last_name, phone, email, GROUP_CONCAT(plots.number SEPARATOR ', ') AS plots
        FROM users
        LEFT JOIN plot_user ON users.user_id = plot_user.user_id
        LEFT JOIN plots ON plot_user.plot_id = plots.plot_id
        WHERE users.user_id = '$user_id'
        GROUP BY users.user_id") or die (DB::error());
        if ($row = DB::fetch_row($q)) {
            return [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'phone' => $row['phone'],
                'email' => $row['email'],
                'plots' => $row['plots'],
            ];
        } else {
            return [
                'id' => 0,
                'first_name' => '',
                'last_name' => '',
                'phone' => '',
                'email' => '',
            ];
        }
    }

    public static function users_list_plots($number) {
        // vars
        $items = [];
        // info
        $q = DB::query("SELECT user_id, plot_id, first_name, email, phone
            FROM users WHERE plot_id LIKE '%".$number."%' ORDER BY user_id;") or die (DB::error());
        while ($row = DB::fetch_row($q)) {
            $plot_ids = explode(',', $row['plot_id']);
            $val = false;
            foreach($plot_ids as $plot_id) if ($plot_id == $number) $val = true;
            if ($val) $items[] = [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'email' => $row['email'],
                'phone_str' => phone_formatting($row['phone'])
            ];
        }
        // output
        return $items;
    }

    public static function users_list($d = []) {
        // vars
        $search = isset($d['search']) && trim($d['search']) ? $d['search'] : '';
        $offset = isset($d['offset']) && is_numeric($d['offset']) ? $d['offset'] : 0;
        $limit = 20;
        $items = [];
        // where
        $where = [];
        if ($search) {
            $where[] = "phone LIKE '%" . $search . "%' 
                        or first_name LIKE '%" . $search . "%'
                        or last_name LIKE '%" . $search . "%'
                        or email LIKE '%" . $search . "%'";
        }

        $where = $where ? "WHERE ".implode(" AND ", $where) : "";


        $q = DB::query("SELECT users.user_id, first_name, last_name, phone, email, last_login, GROUP_CONCAT(plots.number SEPARATOR ', ') as plots
            FROM users
            LEFT JOIN plot_user ON users.user_id = plot_user.user_id
            LEFT JOIN plots ON plot_user.plot_id = plots.plot_id
            ".$where."  GROUP BY users.user_id ORDER BY users.user_id+0 LIMIT ".$offset.", ".$limit.";") or die (DB::error());

        while ($row = DB::fetch_row($q)) {
            $items[] = [
                'id' => (int) $row['user_id'],
                'first_name' => $row['first_name'],
                'last_name' => $row['last_name'],
                'email' => $row['email'],
                'phone' => $row['phone'],
                'plots' => $row['plots'],
                'last_login' => date('Y/m/d', $row['last_login'])
            ];
        }

        // paginator
        $q = DB::query("SELECT count(*) FROM users  ".$where.";");
        $count = ($row = DB::fetch_row($q)) ? $row['count(*)'] : 0;
        $url = 'users';
        if ($search) $url .= '?search='.$search.'&';
        paginator($count, $offset, $limit, $url, $paginator);
        // output
        return ['items' => $items, 'paginator' => $paginator];
    }

    public static function users_fetch($d = []) {
        $info = User::users_list($d);
        HTML::assign('users', $info['items']);
        return ['html' => HTML::fetch('./partials/users_table.html'), 'paginator' => $info['paginator']];
    }

    public static function user_delete_window($d = []) {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $offset = isset($d['offset']) ? preg_replace('~\D+~', '', $d['offset']) : 0;

        $q = "DELETE FROM users WHERE user_id = $user_id";
        DB::query($q) or die (DB::error());

        return User::users_fetch(['offset' =>  $offset]);
    }

    public static function user_edit_window($d = []) {
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        HTML::assign('user', User::user_edit_info($user_id));
        return ['html' => HTML::fetch('./partials/user_edit.html')];
    }

    public static function user_edit_update($d = []) {
        // vars
        $user_id = isset($d['user_id']) && is_numeric($d['user_id']) ? $d['user_id'] : 0;
        $first_name = isset($d['first_name']) ? trim($d['first_name']) : '';
        $last_name = isset($d['last_name']) ? trim($d['last_name']) : '';
        $email = isset($d['email']) && trim($d['email']) ? trim($d['email']) : '';
        $phone = isset($d['phone']) && trim($d['phone']) ? trim($d['phone']) : '';
        $offset = isset($d['offset']) ? preg_replace('~\D+~', '', $d['offset']) : 0;
        $plotNumbers = isset($d['plots']) ? explode(",", $d['plots']) : '';

        // update
        if ($user_id) {
            $set = [];
            $set[] = "first_name='".$first_name."'";
            $set[] = "last_name='".$last_name."'";
            $set[] = "email='".$email."'";
            $set[] = "phone='".$phone."'";
            $set = implode(", ", $set);
            DB::query("UPDATE users SET ".$set." WHERE user_id='$user_id' LIMIT 1;") or die (DB::error());
            DB::query("DELETE from plot_user WHERE user_id='$user_id'");

        } else {
            DB::query("INSERT INTO users (
                first_name,
                last_name,
                email,
                phone
            ) VALUES (
                '".$first_name."',
                '".$last_name."',
                '".$email."',
                '".$phone."'
            );") or die (DB::error());
        }


        if (!$user_id) {
            $user_id = DB::lastId();
        }

        $plotNumbersArray = array_unique(array_filter(array_map('intval', $plotNumbers)));
        $plotIdsArray = [];

        if (!empty($plotNumbersArray)) {
            $numbers = implode(',', $plotNumbersArray);
            $q = DB::query("SELECT plot_id FROM plots WHERE number IN ($numbers)");
            while ($row = DB::fetch_row($q)) {
                $plotIdsArray[] = $row['plot_id'];
            }

            $values = [];
            foreach ($plotIdsArray as $plotId) {
                $values[] = "($user_id, $plotId)";
            }

            if (!empty($values)) {
                DB::query("INSERT INTO plot_user (user_id, plot_id) VALUES " . implode(", ", $values));
            }
        }
        // output
        return User::users_fetch(['offset' => $offset]);
    }
}
