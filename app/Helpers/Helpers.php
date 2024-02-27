<?php

if ( ! function_exists('years')) {
    function years()
    {
        $yearArr = [];
        for ($i = date('Y'); $i > 2022; $i--) {
            $yearArr[$i] = $i;
        }
        return $yearArr;
    }
}

if ( ! function_exists('months')) {
    function months()
    {
        return [
			'01' => 'January',
			'02' => 'February',
			'03' => 'March',
			'04' => 'April',
			'05' => 'May',
			'06' => 'June',
			'07' => 'July',
			'08' => 'August',
			'09' => 'September',
			'10' => 'October',
			'11' => 'November',
			'12' => 'December'
        ];
    }
}


//Per page Limit for paginate
if ( ! function_exists('paginateLimit')) {
    function paginateLimit()
    {
        return request()->limit ?? config('blade-components.paginate_default_limit');
    }
}

//Pagination serial number
if (!function_exists('pagiSerial')) {
    function pagiSerial($records)
    {
        $perPage = paginateLimit();
        return (!empty(request()->page)) ? (($perPage * (request()->page - 1)) + 1) : 1;
    }
}

//url with query string
if ( ! function_exists('qUrl')) {
    function qUrl($queryArr = null, $route = null)
    {
        $route = $route ?? url()->current();
        return $route.qString($queryArr);
    }
}

//Search string get and set an url
if ( ! function_exists('qString')) {
    function qString($queryArr = null)
    {
        if (!empty($queryArr)) {
            $query = '';

            if (!empty($_GET)) {
                $getArray = $_GET;
                unset($getArray['page']);

                foreach ($queryArr as $qk => $qv) {
                    unset($getArray[$qk]);
                }

                $x = 0;
                foreach ($getArray as $gk => $gt) {
                    $query .= ($x != 0) ? '&' : '';
                    $query .= $gk.'='.$gt;
                    $x++;
                }
            }

            $y = 0;
            foreach ($queryArr as $qk => $qv) {
                if ($qv != null) {
                    $query .= ($y != 0 || $query != '') ? '&' : '';
                    $query .= $qk.'='.$qv;
                    $y++;
                }
            }

            return '?'.$query;

        } elseif (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] != null) {
            return '?'.$_SERVER['QUERY_STRING'];
        }
    }
}

//Search Aray get to route redirect with get param
if ( ! function_exists('qArray')) {
    function qArray()
    {
        if (isset($_SERVER['QUERY_STRING'])) {
            return $_GET;
        } else {
            return null;
        }
    }
}

//Date Format
if ( ! function_exists('dateFormat')) {
    function dateFormat($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('d M, Y h:i A', strtotime($date));
            } else {
                return date('d M, Y', strtotime($date));
            }
        }
    }
}

//Date Convert to DB Date Format
if ( ! function_exists('dbDateFormat')) {
    function dbDateFormat($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('Y-m-d h:i A', strtotime($date));
            } else {
                return date('Y-m-d', strtotime($date));
            }
        }
    }
}

//DB Date Format Retrieve to Form Input Format
if ( ! function_exists('dbDateRetrieve')) {
    function dbDateRetrieve($date, $time = null)
    {
        if ($date != null) {
            if ($time) {
                return date('d-m-Y h:i A', strtotime($date));
            } else {
                return date('d-m-Y', strtotime($date));
            }
        }
    }
}

//Time Format
if ( ! function_exists('timeFormat')) {
    function timeFormat($date)
    {
        return date('h:i A',(strtotime($date)));
    }
}

//Two Digit Number Format Function
if ( ! function_exists('numberFormat')) {
    function numberFormat($amount = 0, $coma = null)
    {
        if ($coma) {
            if ($amount == 0)
                return '-';
            else
                return number_format($amount, 2);
        } else {
            return number_format($amount, 2, '.', '');
        }
    }
}

//Showing limited text with '...'
if ( ! function_exists('excerpt')) {
    function excerpt($text, $limit = 200)
    {
        if (strlen(strip_tags($text)) > $limit) {
            return substr(strip_tags($text), 0, $limit).'...';
        } else {
            return strip_tags($text);
        }
    }
}
