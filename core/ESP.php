<?php
class ESP
{

    public static $_resource = null;
    public static $_act = null;

    public static function resource($resource, $act)
    {
        self::$_resource = $resource;
        self::$_act = $act;
    }

    public static function param($name, $default = null)
    {
        $value = $default;
        if (self::is_post()) {
            if (isset($_POST[$name])) {
                $value = $_POST[$name];
            } elseif (isset($_GET[$name])) {
                $value = $_GET[$name];
            }
        } elseif (self::is_get()) {
            if (isset($_GET[$name])) {
                $value = $_GET[$name];
            } elseif (isset($_POST[$name])) {
                $value = $_POST[$name];
            }
        }

        return $value;
    }

    public static function param_uri($seq_index, $default = null)
    {
        if (isset($_GET['__esp_uri'][$seq_index])) {
            return $_GET['__esp_uri'][$seq_index];
        }

        return $default;
    }

    public static function is_get()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'GET';
    }

    public static function is_post()
    {
        return strtoupper($_SERVER['REQUEST_METHOD']) == 'POST';
    }

    public static function get_id_by_request($id = null)
    {
        if ($id == null) {
            if (isset($_GET['id'])) {
                $id = $_GET['id'];
            }
            if (isset($_POST['id'])) {
                $id = $_POST['id'];
            }
        }

        return $id;
    }

    public static function redirect($url = '/', $http_status_code = '302')
    {
        header("Location: $url", true, $http_status_code);
        exit();
    }

    public static function redirect_create()
    {
        self::redirect(self::link_create());
    }

    public static function redirect_read($id = null)
    {

        self::redirect(self::link_read($id));
    }

    public static function redirect_edit($id = null)
    {

        self::redirect(self::link_edit($id));
    }

    public static function redirect_delete($id = null)
    {

        self::redirect(self::link_delete($id));
    }

    public static function redirect_list()
    {

        self::redirect(self::link_list());
    }

    public static function redirect_login()
    {
        self::redirect(self::link_login());
    }

    public static function link_create()
    {
        return "/" . self::$_resource . "/create/";
    }

    public static function link_read($id = null)
    {
        return "/" . self::$_resource . "/read/" . self::get_id_by_request($id);
    }

    public static function link_edit($id = null)
    {
        return "/" . self::$_resource . "/edit/" . self::get_id_by_request($id);
    }

    public static function link_delete($id = null)
    {
        return "/" . self::$_resource . "/delete/" . self::get_id_by_request($id);
    }

    public static function link_list($page = 1)
    {
        return "/" . self::$_resource . "/list/" . $page;
    }

    public static function link_list_next_page($page = 1){
        return self::link_list($page + 1);
    }

    public static function link_list_prev_page($page = 1){
        if ($page == 1){
            return self::link_list(1);
        }
        return self::link_list($page - 1);
    }

    public static function link_login()
    {
        return "/member/login";
    }

    public static function db($table_name = null)
    {
        if ($table_name == null) {
            $table_name = self::$_resource;
        }
        return new ESPDB($table_name);
    }

    public static function part($path, $view_data = [])
    {        
        $template_path = $_SERVER['DOCUMENT_ROOT'] . "/part/$path.php";        
        if (file_exists($template_path)) {
            extract($view_data);
            require($template_path);
        }
    }

    public static function part_header($view_data = [])
    {
        return self::part("common/header");
    }

    public static function part_footer($view_data = [])
    {
        return self::part("common/footer");
    }

    public static function part_auto($path = "", $view_data=[]){
        if ($path == ""){
            return self::part(self::$_resource . "/" . self::$_act, $view_data);
        }

        return self::part(self::$_resource . "/" . self::$_act . "." .$path, $view_data);
    }

    public static function auto_save($table_name = null, $use_columns = [])
    {
        if (self::$_act == "create") {
            if (self::is_post()) {
                $id = ESP::db($table_name)->param($use_columns)->insert();
                self::redirect_read($id);
            }
        } elseif (self::$_act == "edit") {
            if (self::is_post()) {
                $upd_result = ESP::db($table_name)->param($use_columns)->update();
                if ($upd_result) {
                    self::redirect_read();
                } else {
                    self::response_404_page_not_found();
                }
            }
        }
    }

    public static function auto_find($table_name = null, $id = null)
    {
        $result = self::db($table_name)->find($id);
        if ($result->is_empty()) {
            self::response_404_page_not_found();
        }

        return $result;
    }

    public static function auto_delete($table_name = null, $id = null)
    {
        $result = self::db($table_name)->delete($id);
        if ($result) {
            ESP::redirect_list();
        } else {
            ESP::response_404_page_not_found();
        }
    }

    public static function array_to_espdata($array)
    {
        $data = new EspData();
        foreach ($array as $key => $val) {
            $data->$key = $val;
        }
        return $data;
    }

    public static function trim($str)
    {
        return preg_replace("/(^\s+)|(\s+$)/us", "", $str);
    }

    public static function response_404_page_not_found()
    {
        echo "PAGE NOT FOUND";
        header("HTTP/1.0 404 Not Found");
        exit();
    }

    public static function response_json($esp_data){
        $json_value = $esp_data->to_json();
        header('Content-type: application/json');
        echo $json_value;
        exit();
    }

    // session
    public static function session_start()
    {
        if (isset($_SESSION) == false) {
            session_start();
        }
    }

    public static function session_set($key, $val)
    {
        self::session_start();
        $_SESSION[$key] = $val;
    }

    public static function session_get($key, $default = null)
    {
        self::session_start();
        if (isset($_SESSION[$key])) {
            return $_SESSION[$key];
        }
        return $default;
    }

    public static function session_remove($key)
    {
        self::session_start();
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function session_has_key($key)
    {
        return self::session_get($key) ? true : false;
    }

    // login
    public static function is_login()
    {
        return self::session_has_key("login_id");
    }

    public static function login_required()
    {
        if (self::is_login() == false) {
            self::redirect_login();
        }
    }

    public static function login($login_id)
    {
        self::session_set("login_id", $login_id);
    }

    public static function login_id()
    {
        return self::session_get("login_id");
    }

    // is_author
    public static function is_author()
    {
        $model = self::auto_find();
        return $model->author_id == self::login_id();
    }

    public static function author_matched()
    {
        ESP::login_required();
        return self::is_author();
    }

    public static function html_ul_open($css_class=null, $html_id=null){
        $css_class = $css_class == null ? "" : " class='$css_class' ";
        $html_id = $html_id == null ? "" : " id='$html_id' ";
        echo "<ul $css_class $html_id>";
    }

    public static function html_ul_close(){
        echo "</ul>";
    }
}

class ESPDB
{
    private $table_name;
    private $column_values = [];
    public function __construct($table_name)
    {
        $this->table_name = $table_name;
    }

    public function param($use_columns = [])
    {
        if (ESP::is_post()) {
            $this->column_values = $_POST;

            foreach ($_GET as $key => $val) {
                if (array_key_exists($key, $_POST) == false) {
                    $this->column_values[$key] = $val;
                }
            }
        } elseif (ESP::is_get()) {
            $this->column_values = $_GET;

            foreach ($_POST as $key => $val) {
                if (array_key_exists($key, $_GET) == false) {
                    $this->column_values[$key] = $val;
                }
            }
        }

        $temp_arr = [];
        foreach ($use_columns as $use_column) {
            if (array_key_exists($use_column, $this->column_values)) {
                $temp_arr[$use_column] = $this->column_values[$use_column];
            }
        }

        $this->column_values = $temp_arr;

        return $this;
    }

    private function get_pdo()
    {
        require(dirname(__FILE__) . "/esp.config.PHP");

        $host = $db_config['host'];
        $port = $db_config['port'];
        $dbname = $db_config['dbname'];
        $charset = $db_config['charset'];
        $username = $db_config['username'];
        $db_pw = $db_config['password'];
        $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
        $pdo = new PDO($dsn, $username, $db_pw);
        return $pdo;
    }

    public function execute_last_id($query, $param = [])
    {
        $pdo = $this->get_pdo();
        try {
            $st = $pdo->prepare($query);
            $result = $st->execute($param);
            $last_id = $pdo->lastInsertId();
            $pdo = null;
            if ($result) {
                return $last_id;
            } else {
                return false;
            }
        } catch (PDOException $ex) {
            return false;
        }
    }

    public function execute($query, $param = [])
    {
        $pdo = $this->get_pdo();
        try {
            $st = $pdo->prepare($query);
            $result = $st->execute($param);
            $pdo = null;
            return $result;
        } catch (PDOException $ex) {
            return false;
        }
    }

    public function execute_fetch_all($query, $param = [])
    {
        try {
            $pdo = $this->get_pdo();
            $st = $pdo->prepare($query);
            $st->execute($param);
            $result = $st->fetchAll(PDO::FETCH_ASSOC);
            $pdo = null;

            $espdatas = [];
            foreach ($result as $row) {
                $esp_row = ESP::array_to_espdata($row);
                array_push($espdatas, $esp_row);
            }
            return $espdatas;
        } catch (Exception $ex) {
            return [];
        }
    }

    public function execute_fetch_first($query, $param = [])
    {
        $fetch_result = $this->execute_fetch_all($query, $param);
        if (count($fetch_result) > 0) {
            return $fetch_result[0];
        }

        return new EspData();
    }

    public function table_list()
    {
        require(dirname(__FILE__) . "/esp.config.PHP");
        $dbname = $db_config['dbname'];
        $query = "select table_name from INFORMATION_SCHEMA.tables where table_schema = :table_schema order by table_name";
        $result = $this->execute_fetch_all($query, ['table_schema' => $dbname]);
        return $result;
    }

    public function table_exist()
    {
        require(dirname(__FILE__) . "/esp.config.PHP");
        $dbname = $db_config['dbname'];
        $query = "select table_name from INFORMATION_SCHEMA.tables where table_schema = :table_schema and table_name = :table_name";
        $result = $this->execute_fetch_first($query, ['table_schema' => $dbname, 'table_name' => $this->table_name]);
        return count($result) > 0;
    }

    public function column_list()
    {
        require(dirname(__FILE__) . "/esp.config.PHP");
        $dbname = $db_config['dbname'];
        $query = "select column_name, column_default, is_nullable, data_type from INFORMATION_SCHEMA.columns where table_schema = :table_schema and table_name = :table_name";
        return $this->execute_fetch_all($query, ['table_schema' => $dbname, 'table_name' => $this->table_name]);
    }

    public function column_user_define_list()
    {
        $column_list = $this->column_list();
        $column_list = array_filter($column_list, function ($item) {
            $item = $item->items();
            if ($item['column_name'] == "id" || $item['column_name'] == "insert_date" || $item['column_name'] == "update_date") {
                return false;
            }
            return true;
        });
        return $column_list;
    }

    private function make_terms($where_terms = [])
    {
        $terms = array();
        foreach ($where_terms as $key => $value) {
            array_push($terms, "$key = :$key");
        }

        // and 의 공백에 주의
        $where = implode(" and ", $terms);

        if (ESP::trim($where) !== '') {
            $where = " where " . $where;
        }

        return $where;
    }

    private function make_orderby($orderby){
        if (ESP::trim($orderby) !== '') {
            $orderby = " order by " . $orderby;
        }
        return $orderby;
    }
    
    private function make_limit($limit){
        if ($limit == null){
            return "";
        }
    
        return " limit $limit";
    }

    public function insert()
    {
        $columns = array_keys($this->column_values);
        $value_placeholders = array_map(
            function ($key) {
                return ":$key";
            },
            $columns
        );

        $strColumn = implode(",", $columns);
        $strValuePlaceHolders = implode(",", $value_placeholders);
        $query = "insert into {$this->table_name} ($strColumn) values ($strValuePlaceHolders)";

        return $this->execute_last_id($query, $this->column_values);
    }

    public function find($id = null)
    {
        $id = ESP::get_id_by_request($id);
        if ($id == null) {
            return new EspData();
        }

        $query = " select * from {$this->table_name} where id=:id limit 1";
        return $this->execute_fetch_first($query, ['id' => $id]);
    }

    public function all($where_terms = [], $order_by=null, $limit=null)
    {
        $terms = $this->make_terms($where_terms);
        $query = " select * from {$this->table_name} where $terms ";

        $order_by = $this->make_orderby($order_by);
        $limit = $this->make_limit($limit);
        $query = "select * from {$this->table_name} $terms $order_by $limit";        
        $result = $this->execute_fetch_all($query, $where_terms);
        return $result;
    }

    public function pagenate($page_no, $where_terms = [], $order_by='insert_date desc', $count_per_page=10){
        $offset = ($page_no -1) * $count_per_page;
        return $this->all($where_terms, $order_by, "$offset,$count_per_page");
    }

    public function count($where_terms){
        $terms = $this->make_terms($where_terms);
        $query = " select count(id) as cnt from {$this->table_name} where $terms ";
        $result = $this->execute_fetch_all($query, $where_terms);
        return $result[0]['cnt'];
    }

    public function exist($where_terms){
        $result = $this->count($where_terms);
        return $result > 0;
    }

    public function update($id = null)
    {
        $id = ESP::get_id_by_request($id);
        if ($id == null) {
            return false;
        }

        $where_terms = ['id' => $id];

        $columns = array_keys($this->column_values);

        // 배열의 각 아이템에 항목 적용시키기.
        $placeholders = array_map(
            function ($key) {
                return "$key = :$key";
            },
            $columns
        );

        $strPlaceHolders = implode(",", $placeholders);

        $terms = $this->make_terms($where_terms);
        $total_kv = array_merge($this->column_values, $where_terms);

        $query = "update {$this->table_name} set $strPlaceHolders $terms";
        $result = $this->execute($query, $total_kv);
        return $result;
    }

    public function delete($id = null)
    {
        $id = ESP::get_id_by_request($id);
        if ($id == null) {
            return false;
        }

        $where_terms = ['id' => $id];
        $terms = $this->make_terms($where_terms);
        $query = "delete from {$this->table_name} $terms";
        return $this->execute($query, $where_terms);
    }
}

class EspData
{
    private $attributes = [];

    public function __get($name)
    {
        return $this->get($name, "");
    }

    public function __set($name, $value)
    {
        $this->val($name, $value);
    }

    public function get($name, $default = null)
    {
        if (array_key_exists($name, $this->attributes)) {
            return $this->attributes[$name];
        } else {
            return $default;
        }
    }

    public function val($name, $value)
    {
        $this->attributes[$name] = $value;
        return $this;
    }

    public function is_empty()
    {
        return count($this->attributes) == 0;
    }

    public function items()
    {
        return $this->attributes;
    }

    public function to_json(){
        return json_encode($this->attributes);
    }
}
