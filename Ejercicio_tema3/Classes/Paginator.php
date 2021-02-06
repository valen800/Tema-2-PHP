<?php
include_once "Constants.php";
include_once "SQLDatabase.php";

class Paginator {

    private $currentPage;
    private $limit;
    private $totalRecords;
    private $conn;

    public function __construct(
        $limit = Constants::MAX_ROWS_PAGE) {
            $this->conn = new SQLDatabase();
            $this->limit = $limit;
            $this->totalRecords = $this->setTotalRecords();
    }

    public function renderData() {
        // param true is for convert stdObject to array
        $arr = json_decode($this->getData(), true);

        foreach ($arr as $key => $value) {
            echo '<tr>';
            echo '<td>'.$value['id'].'</td>';
            echo '<td>'.$value['nom'].' '.$value['cognoms'].'</td>';
            echo '</tr>';
        }
    }

    protected function getData() {
        $start = 0;
        
        if ($this->getCurrentPage() > 1) {
            $start = ($this->getCurrentPage() * $this->limit) - $this->limit;
        }

        $this->conn->connection();
        $data = $this->conn->query("SELECT id,nom,cognoms FROM contactes LIMIT $start,$this->limit");
        $this->conn->disconnect();

        $json = mysqli_fetch_all($data, MYSQLI_ASSOC);
        return json_encode($json);
    }

    public function setTotalRecords() {

        $this->conn->connection();
        $data = $this->conn->query("SELECT id FROM contactes");
        $this->conn->disconnect();

        $numRows = $data->num_rows;

        return $numRows;
    }

    public function getTotalRecords() {
        return $this->totalRecords;
    }

    public function getTotalPages($rows) {

        $totalPages = ceil($rows / $this->limit);
        return $totalPages;
    }

    protected function getCurrentPage() {
        $this->currentPage = (isset($_GET['page']) && is_numeric($_GET['page']) ) ? $_GET['page'] : 1;

        return $this->currentPage;
    }

    public function prevPage(){
        $page = 0;
        $currentPage = $this->getCurrentPage();
        $totalPages = $this->getTotalPages($this->totalRecords);

        if ($currentPage > 1) {
            $page = $currentPage - 1;
        } else {
            $page = 1;
        }

        return $page;
    }

    public function nextPage(){
        $page = 0;
        $currentPage = $this->getCurrentPage();
        $totalPages = $this->getTotalPages($this->totalRecords);

        if ($currentPage < $totalPages) {
            $page = $currentPage + 1;
        } else {
            $page = $totalPages;
        }

        return $page;
    }
}
//https://steemit.com/utopian-io/@alfarisi94/pagination-with-php-oop-system-1-basic-oop-class-fetch-data-with-pdo-database-use-function-in-a-class
//https://steemit.com/utopian-io/@alfarisi94/pagination-with-php-oop-system-2-create-the-previous-page-create-the-next-page-active-class-in-php
?>