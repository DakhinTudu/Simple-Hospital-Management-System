<?php
include('include/config.php');

if(isset($_POST['update_data']))
{
 $contact=$_POST['contact'];
 $status=$_POST['status'];
 $query="update appointmenttb set payment='$status' where contact='$contact';";
 $result=mysqli_query($con,$query);
 if($result)
  header("Location:updated.php");
}

if (!function_exists('display_specs')) {
  function display_specs() {
    global $con;
    $query="select distinct(spec) from doctb";
    $result=mysqli_query($con,$query);
    while($row=mysqli_fetch_array($result))
    {
      $spec=$row['spec'];
      echo '<option data-value="'.$spec.'">'.$spec.'</option>';
    }
  }
}

if (!function_exists('display_docs')) {
  function display_docs()
  {
   global $con;
   $query = "select * from doctb";
   $result = mysqli_query($con,$query);
   while( $row = mysqli_fetch_array($result) )
   {
    $username = $row['username'];
    $price = $row['docFees'];
    $spec = $row['spec'];
    echo '<option value="' .$username. '" data-value="'.$price.'" data-spec="'.$spec.'">'.$username.'</option>';
   }
  }
}

if(isset($_POST['doc_sub']))
{
 $username=$_POST['username'];
 $query="insert into doctb(username)values('$username')";
 $result=mysqli_query($con,$query);
 if($result)
  header("Location:adddoc.php");
}

/**
 * Pagination helper to get current page and offset
 */
function hms_get_pagination_data($con, $table, $where = "1", $params = [], $types = "") {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($page < 1) $page = 1;
    $limit = RECORDS_PER_PAGE;
    $offset = ($page - 1) * $limit;

    $countQuery = "SELECT COUNT(*) as total FROM $table WHERE $where";
    $stmt = mysqli_prepare($con, $countQuery);
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $total = 0;
    if ($row = mysqli_fetch_assoc($result)) {
        $total = $row['total'];
    }
    $totalPages = ceil($total / $limit);

    return [
        'page' => $page,
        'limit' => $limit,
        'offset' => $offset,
        'total' => $total,
        'totalPages' => $totalPages
    ];
}

/**
 * Render pagination links preserving filters
 */
function hms_render_pagination($currentPage, $totalPages, $tabId = "") {
    if ($totalPages <= 1) return "";

    $html = '<nav aria-label="Page navigation" class="mt-4"><ul class="pagination justify-content-center">';
    
    // Preserve current GET parameters
    $get_params = $_GET;
    unset($get_params['page']); // We will set this manually
    $query_string = http_build_query($get_params);
    $query_prefix = !empty($query_string) ? $query_string . '&' : '';

    // Previous button
    $prevPage = $currentPage - 1;
    $disabled = ($currentPage <= 1) ? "disabled" : "";
    $url = "?{$query_prefix}page=$prevPage" . ($tabId ? "#$tabId" : "");
    $html .= '<li class="page-item ' . $disabled . '"><a class="page-link" href="' . $url . '"><i class="fa fa-angle-left"></i></a></li>';

    // Page numbers (limited range for responsiveness)
    $range = 2;
    for ($i = 1; $i <= $totalPages; $i++) {
        if($i == 1 || $i == $totalPages || ($i >= $currentPage - $range && $i <= $currentPage + $range)) {
            $active = ($i == $currentPage) ? "active" : "";
            $url = "?{$query_prefix}page=$i" . ($tabId ? "#$tabId" : "");
            $html .= '<li class="page-item ' . $active . '"><a class="page-link" href="' . $url . '">' . $i . '</a></li>';
        } elseif ($i == $currentPage - $range - 1 || $i == $currentPage + $range + 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
        }
    }

    // Next button
    $nextPage = $currentPage + 1;
    $disabled = ($currentPage >= $totalPages) ? "disabled" : "";
    $url = "?{$query_prefix}page=$nextPage" . ($tabId ? "#$tabId" : "");
    $html .= '<li class="page-item ' . $disabled . '"><a class="page-link" href="' . $url . '"><i class="fa fa-angle-right"></i></a></li>';

    $html .= '</ul></nav>';
    return $html;
}

/**
 * Build dynamic WHERE clause based on filters
 */
function hms_build_filter_where($filters) {
    $where = "1";
    $params = [];
    $types = "";

    foreach ($filters as $col => $val) {
        if (!empty($val)) {
            if ($col === 'start_date') {
                $where .= " AND appdate >= ?";
                $params[] = $val;
                $types .= "s";
            } elseif ($col === 'end_date') {
                $where .= " AND appdate <= ?";
                $params[] = $val;
                $types .= "s";
            } elseif ($col === 'status') {
                if ($val === 'confirmed') {
                    $where .= " AND userStatus='1' AND doctorStatus='1'";
                } elseif ($val === 'cancelled') {
                    $where .= " AND (userStatus='0' OR doctorStatus='0')";
                }
            } else {
                $where .= " AND $col = ?";
                $params[] = $val;
                $types .= is_numeric($val) ? "i" : "s";
            }
        }
    }
    return ['where' => $where, 'params' => $params, 'types' => $types];
}

/**
 * Escaping utility
 */
if (!function_exists('hms_esc')) {
    function hms_esc($str) {
        return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
    }
}
?>
