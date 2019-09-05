<?php

if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

/**
 * This file defines the library for the Datatable
 *
 * @filesource : MyDataTables.php
 * @author amitabh
 */
class MyDataTables
{

    public $result = null;
    public $encrypt_date = null;
    public $_columns, $_table, $_where, $_order, $_id, $_db;
    public $_title;
    public $_actions;
    public $_gridSearch = false;
    public $_defaultSortCol = "", $_defaultSortDir = "";
    public $_table_view_id = "table_rows";
    public $_showExportOptions = false;
    public $_customQuery = "";
    public $_enableShowAll = false;
    public $_baseUrl = '';
    public $_allowAdd = true;
    public $_customAddId = "0";

    /**
     * This function must be called before assigning values via $this->mydatatables->XXX
     * when generating multiple datatable
     */
    public function init()
    {
        $this->result = null;
        $this->encrypt_date = null;
        $this->_columns = array();
        $this->_table = "";
        $this->_where = "";
        $this->_order = "";
        $this->_id = "";
        $this->_db = "";
        $this->_title = "";
        $this->_actions = null;
        $this->_gridSearch = false;
        $this->_defaultSortCol = "";
        $this->_defaultSortDir = "";
        $this->_table_view_id = "table_rows";
        $this->_baseUrl = '';
        $this->_allowAdd = true;
        $this->_customAddId = "0";
    }

    /**
     * This method allows you to define the columns for the datatable, you can add many columns by repeatedly calling this function.
     *
     * @param string $field
     *         : This contains the fields enclosed in curly braces that will be displayed in the data-column;
     *         You can combine multiple fields ex. {first_name} {last_name}
     * @param string $field_type
     *         : This defines the type of the column required for datatable sort
     * @param string $header:
     *         This defines the header text for the column
     * @param string $class
     *         : The stylesheet class definition (optional)
     * @param boolean $is_sortable
     *         : True/False to allow sorting of the column
     * @param boolean $is_searchable
     *         : True / False to allow searching of the column
     * @param boolean $is_visisble:
     *         True/False to show / hide the column
     * @param string $misc: nullable, extra information to be associated with the column; TODO
     */
    public function addColumn($field, $field_type = "html", $header = "", $class = "", $is_sortable = true, $is_searchable = true, $is_visisble = true, $misc = null)
    {
        $this->_columns[] = array(
            "field" => "$field",
            "field_type" => "$field_type",
            "header" => "$header",
            "class" => "$class",
            "is_sortable" => $is_sortable,
            "is_searchable" => $is_searchable,
            "is_visisble" => $is_visisble,
            "misc" => $misc,
        );
    }

    /**
     * This function defines the actions ex.
     * Edit / Delete links for each row.
     *
     * @param string $text:
     *         Text to be displayed to the user
     * @param string $link:
     *         Hyperlink to the text
     * @param string $class:
     *         Stylesheet (optional) definition
     * @param string $icon:
     *         Icon (optional)
     */
    public function addAction($text, $link, $class = "", $icon = "", $angular = false)
    {
        $this->_actions[] = array(
            "text" => $text,
            "link" => $link,
            "class" => $class . " dtActions",
            "icon" => $icon,
            "angular" => $angular,
        );
    }

    /**
     * This function defines the table/view name from which data needs to be fetched
     *
     * @param string $table_name
     */
    public function setTableViewID($table_view_id)
    {
        $this->_table_view_id = $table_view_id;
    }

    /**
     * This function defines the table/view name from which data needs to be fetched
     *
     * @param string $table_name
     */
    public function setTableName($table_name)
    {
        $this->_table = $table_name;
    }

    public function setDB($db)
    {
        $this->_db = $db;
    }

    /**
     * Set the unique field to identify the row (mandatory if you are using action
     *
     * @param string $id
     */
    public function setID($id)
    {
        $this->_id = $id;
    }

    /**
     * Set the search criteria (optional)
     *
     * @param string $where
     */
    public function setWhere($where)
    {
        $this->_where = $where;
    }

    /**
     * The title for the datatable;
     *
     * @param String $title
     */
    public function setTitle($title)
    {
        $this->_title = $title;
    }

    /**
     * The comma seperated (ex.
     * firstname, lastname desc) ordering in sequence for sorting the columns
     *
     * @param string $order
     */
    public function setOrder($order)
    {
        $this->_order = $order;
    }

    public function setGridSearchAll($srch = false)
    {
        $this->_gridSearch = $srch;
    }

    public function setCustomQuery($customQuery = "")
    {
        $this->_customQuery = $customQuery;
    }

    public function setShowExportOptions($options = false)
    {
        $this->_showExportOptions = $options;
    }

    public function setEnableShowAll($option = false)
    {
        $this->_enableShowAll = $option;
    }

    public function SetBaseUrl($url = false)
    {
        $this->_baseUrl = $url;
    }

    public function SetAllowAdd($allow_add = true)
    {
        $this->_allowAdd = $allow_add;
    }

    public function SetCustomAddId($customAddId = "0")
    {
        $this->_customAddId = $customAddId;
    }

    /**
     * This function allows you to change default sorting (when DT loads)
     * @param string $sortColumnIndex (Column No. : 0, 1, .... etc.)
     * @param string $sortColumnDirection ('asc' or 'desc')
     */
    public function setDefaultSortColumn($sortColumnIndex = "", $sortColumnDirection = "")
    {
        $this->_defaultSortCol = $sortColumnIndex;
        $this->_defaultSortDir = $sortColumnDirection;
    }

    /**
     * Internal function to retrieve the uniue field names from the columns defined for the datatable
     *
     * @return string fieldlist (csv)
     */
    private function _getFieldList()
    {
        $fields = array();
        if ($this->_columns) {
            foreach ($this->_columns as $col) {
                $output_array = array();
                $col_name = $col["field"];
                $col_name = str_replace("{{", "", str_replace("}}", "", $col_name));
                preg_match_all("/\{.*?\}/ixm", $col_name, $output_array);
                if (count($output_array) > 0 && count($output_array[0]) > 0) {

                    foreach ($output_array[0] as $a) {
                        if (stripos($a, ":dt") || stripos($a, ":en")) {
                            $p = explode(":", $a);
                            $a = $p[0] . "}";
                        }
                        $fields[] = str_replace("{", "", str_replace("}", "", $a));
                    }
                }
            }
        }
        if ($this->_actions) {
            foreach ($this->_actions as $col) {
                $output_array = array();
                preg_match_all("/\{.*?\}/ixm", $col["link"], $output_array);
                if (count($output_array) > 0 && count($output_array[0]) > 0) {
                    foreach ($output_array[0] as $a) {
                        if (stripos($a, ":dt") || stripos($a, ":en")) {
                            $p = explode(":", $a);
                            $a = $p[0] . "}";
                        }
                        $fields[] = str_replace("{", "", str_replace("}", "", $a));
                    }
                }
            }
        }

        if (count($fields)) {
            $id_output_array = array();
            preg_match_all("/\{.*?\}/ixm", $this->_id, $id_output_array);
            foreach ($id_output_array[0] as $a) {
                $fields[] = str_replace("{", "", str_replace("}", "", $a));
            }
            $fields = array_unique($fields);
            return implode(",", $fields);

        } else {
            return "*";
        }
    }

    private function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }

    private function endsWith($haystack, $needle)
    {
        return $needle === "" || substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * This returns the generated html, table id, number of rows to used to render the datatable;
     *
     * @return NULL|multitype:string unknown
     */
    public function generate()
    {
        //TODO: add the "misc", functionality
        $CI = &get_instance();
        //   $CI->load->library('MCrypt');
        $this->encrypt_date = date('U');

        $fields = str_replace("##", "", $this->_getFieldList());

        $where = $this->_where;
        $order = $this->_order;
        $table = $this->_table;

        if (strlen(trim($this->_customQuery)) > 0) {
            $query = $this->_customQuery;
        } else {
            $query = "SELECT $fields FROM $table" . (trim($where) ? " WHERE $where" : "") . (trim($order) ? " ORDER BY $order " : "");
        }
        // echo $query;
        // exit;
        $obj = &CI::get_instance();
        $obj->load->helper('url');
        $obj->load->library('parser');
        $obj->load->driver('session');

        if ( $this->result != null  ) {
            //already have result no need to regenerate query; so that we can process the fetched the rows and regenerate; sideeffect this generate runs twice;
        } else {
            if ($this->_db != "") {
                $db = $obj->load->database($this->_db, true);
                $res = $db->query($query);
            } else {
                $obj->load->database();
                $res = $obj->db->query($query);
            }
        }

        $table_id = preg_replace('/\s+/', '', $this->_title);

        $table = '<div class="table"><table class="table table-striped table-condensed table-hover" id="' . $table_id . '"  name="' . $table_id . '" style="width:100%;" >';
        $table_header = "<thead><tr>";
        $search = "";
        $has_search = false;
        $dt_js_columns = array();

        if (strlen(trim($this->_customQuery)) > 0) {

        }

        foreach ($this->_columns as $col) {
            if ($col["is_visisble"]) {
                $col_id = base64_encode(preg_replace('/\s+/', '', $col["header"]));
                // SET COLUMN ATTRIBUTE
                $dt_js_columns[] = $this->set_columns_attribute($col);
                $table_header .= "<th class='" . ($col["is_searchable"] ? "mSearch" : "") . " bg-primary!important'>" . $col["header"] . "</th>";
            }
        }
        if ($this->_actions) {

            $btnAdd = '';
            if ($this->_allowAdd) {
                $btnAdd = '<button id="" type="button" class="dtActions btn btn-primary" style="margin:4px;" tooltip="Add" onClick="editRow(\'' . $this->_customAddId . '\')">'
                    . '<i class="fa fa-plus"></i> Add</button> &nbsp; ';
            }

            $table_header .= "<th align=\"center\"><div class=\"dtActions\" align=\"center\">" . $btnAdd . "</div></th>";
            $dt_js_columns[] = array(
                //"mDataProp" => null,
                "bSortable" => false,
                "bSearchable" => false,
            );
        }

        $table_header .= "</tr></thead>";

        $table_footer = str_replace("thead", "tfoot", $table_header);
        $table_header .= $table_footer;

        if (isset($this->result) && $this->result) {
            $numofrows = count($this->result);
        } else {
            $this->result = $res->result_array();
            $numofrows = $res->num_rows();
        }

        if ($numofrows) {
            $tr = "";

            // /get the ids
            $id_row_array = array();
            preg_match_all("/\{.*?\}/ixm", $this->_id, $id_row_array);
            foreach ($id_row_array[0] as $a) {
                $idrows[] = $a;
            }
            $idrows = array_unique($idrows);
            
            // Add for the data rows
            foreach ($this->result as $row) {
            	
                $tr_id = array();
                // $tr_id = $obj->session->userdata ( 'key' );
                // get the multiple ids
                if ($idrows) {
                    foreach ($idrows as $idfld) {
                        $tr_id[] = $obj->parser->parse_string($idfld, $row, true);
                    }
                }
                $tr_id = base64_encode(json_encode($tr_id));

                $tr .= "<tr id=\"$tr_id\">";

                foreach ($this->_columns as $col) {
                    if ($col["is_visisble"]) {
                        $col_name = $col["field"];
                        //$col_name = str_replace("{{", "", str_replace("}}","", $col_name));
                        if (stristr($col_name, "##")) {
                            preg_match_all('/{(.*?)}/', $col_name, $matches);
                            foreach ($matches[1] as $key) {
                                if (stristr($key, "##") && $row[str_ireplace("##", "", $key)]) { /* to avoid blank decryption */
                                    $row[str_ireplace("##", "", $key)] = base64_decode($row[str_ireplace("##", "", $key)]);
                                }
                            }
                            $col_name = str_replace("##", "", $col_name);
                        }
                        if (stristr($col_name, ":dt")) {
                            preg_match_all('/{(.*?)}/', $col_name, $matches);
                            foreach ($matches[1] as $key) {
                                if (stristr($key, ":dt")) {
                                    $x = explode(":", $key);
                                    $dt_format = isset($x[2]) && $x[2] ? $x[2] : "";
                                    $c = str_replace("{", "", $x[0]);
                                    $val = $row[$c];
                                    $formated_date = date($dt_format, strtotime($val));
                                    $col_name = str_replace("{" . $key . "}", $formated_date, $col_name);
                                }
                            }
                        }
                        if (stristr($col_name, ":en")) {
                            preg_match_all('/{(.*?)}/', $col_name, $matches);
                            foreach ($matches[1] as $key) {
                                if (stristr($key, ":en")) {
                                    $xx = explode(":", $key);
                                    $cc = str_replace("{", "", $xx[0]);
                                    $valx = $row[$cc];
                                    // 26Nov2014
                                    $val = "";
                                    $arrid = array(
                                        "$cc" => $valx,
                                        "dt" => $this->encrypt_date,
                                        "id" => $tr_id,
                                    );
                                    // $formated_val = $val ? base64_encode(json_encode($arrid)) : "";
                                    // 26Nov2014
                                    $formated_val = $val ? "" : base64_encode(json_encode($arrid));
                                    $col_name = str_replace("{" . $key . "}", $formated_val, $col_name);
                                }
                            }
                        }

                        $td = $obj->parser->parse_string($col_name, $row, true); 
                        $class = $col["class"] ? " class=\"" . $col["class"] . "\"" : "";
                        $cname = str_replace(" ", "__", str_replace("{", "", str_replace("}", "", $col_name)));

                        $cls = $td ? "text-primary" : "text-red";

                        switch (strtolower($col["field_type"])) {
                            ////TODO : btnYesNo + btnActiveInactive
                            case 'yesno':
                                $td = strip_tags($td) ? "Yes" : "No";
                                $tr .= "<td align=\"center\"$class>$td</td>";
                                break;
                            case "btnyesno":
                                $td = strip_tags($td) ? "Yes" : "No";
                                $tr .= "<td align=\"center\"$class>" .
                                    "<button type=\"button\" name=\"$cname\" class=\"btn btn-default $cls\"" .
                                    "onClick=\"btnyesno('$table_id','$cname','$tr_id')\">" .
                                    "<b>$td</b></button></td>";
                                break;
                            case 'activeinactive':
                                $td = strip_tags($td) ? "Active" : "No";
                                $tr .= "<td align=\"center\"$class>$td</td>";
                                break;
                            case "btnactiveinactive":
                                $td = strip_tags($td) ? "Active" : "No";
                                $tr .= "<td align=\"center\"$class>" .
                                    "<button type=\"button\" name=\"$cname\" class=\"btn btn-default $cls\"" .
                                    "onClick=\"btnactiveinactive('$table_id','$cname','$tr_id')\">" .
                                    "<b>$td</b></button></td>";
                                break;
                            case "tickcross":
                                $td = strip_tags($td) ? "&check;" : "&cross;";
                                $tr .= "<td align=\"center\"$class><span name=\"$cname\" class=\"$cls\">$td</span></td>";
                                break;
                            case "btntickcross":
                                $td = strip_tags($td) ? "&check;" : "&cross;";
                                $tr .= "<td align=\"center\"$class>" .
                                    "<button type=\"button\" name=\"$cname\" class=\"btn btn-default $cls\"" .
                                    "onClick=\"btntickcross('$table_id','$cname','$tr_id')\">" .
                                    "<b>$td</b></button></td>";
                                break;
                            default:
                                $tr .= "<td $class>$td</td>";
                                break;
                        }
                    }
                }
                // ACTIONS
                $aa = "";
                // var_dump($this->_actions);
                if ($this->_actions) {
                    foreach ($this->_actions as $action) {
                        if (stristr($action["link"], ":en")) {
                            preg_match_all('/{(.*?)}/', $action["link"], $matches);
                            foreach ($matches[1] as $key) { 
                                if (stristr($key, ":en")) {
                                    $xx = explode(":", $key);
                                    $cc = str_replace("{", "", $xx[0]);
                                    // 26Nov2014
                                    $valx = $row[$cc];
                                    $val = "";
                                    // ****************
                                    $arrid = array(
                                        "$cc" => $valx,
                                        "dt" => $this->encrypt_date,
                                        "id" => $tr_id,
                                    );
                                    // $formated_val = $val ? base64_encode(json_encode($arrid)) : "";
                                    // 26Nov2014
                                    $formated_val = $val ? "" : base64_encode(json_encode($arrid));
                                    $action["link"] = str_replace("{" . $key . "}", $formated_val, $action["link"]);
                                }
                            }
                        }
                        $link = $obj->parser->parse_string($action["link"], $row, true);
//                        $aa .= '<button type="button" class="btn '
                        //                                . (stristr($action["icon"], "close") ? "btn-danger" : (stristr($action["icon"], "pencil") ? "btn-primary" : "btn-default"))
                        //                                . ' btn-circle" style="margin:4px;" data-toggle="tooltip" data-original-title="' . $action ["text"] . '" '
                        //                                . ($action ["angular"] ? 'onClick="' : 'onClick="') . $link . '" >'
                        //                                . '<i class="' . $action ["icon"] . ($action ["class"] ? " " . $action ["class"] : "")
                        //                                //. ' " data-original-title="' . $action ["text"] . '" data-toggle="tooltip" data-placement="top" title="" '
                        //                                . '"></i></button> &nbsp; ';
                        $aa .= '<button type="button" class="dtActions btn '
                            . (stristr($action["icon"], "close") ? "btn-danger" : (stristr($action["icon"], "pencil") ? "btn-primary" : "btn-default"))
                            . ' btn-circle" style="margin:4px;" tooltip="' . $action["text"] . '" '
                            . ($action["angular"] == true ? 'ng-click="' : 'onClick="') . $link . '" >'
                            . '<i class="' . $action["icon"] . ($action["class"] ? " " . $action["class"] : "")
                            . '"></i></button> &nbsp; ';
                    }
                    if ($aa) {
                        $aa = "<td align='center' class=\"dtActions\">$aa</td>";
                    }

                }
                $tr .= $aa . "</tr>";
            }
            $table .= $table_header . "<tbody>" . $tr . "</tbody></table></div>";
        } else {
            $colspan = count($this->_columns);
            if ($this->_actions) {
                $colspan++;
            }

            $table .= $table_header . "<tbody></tbody></table>";
        }
        $tname = "table_" . str_replace(" ", "_", $table_id);
        $buttons = "";
        $dom = "";
        if ($this->_showExportOptions == true) {
            $buttons = 'buttons: [\'copyHtml5\',\'excelHtml5\',\'csvHtml5\',\'pdfHtml5\',\'print\' ],';
            $dom = "B";
        }

        $csrf = array(
            'name' => $CI->security->get_csrf_token_name(),
            'hash' => $CI->security->get_csrf_hash(),
        );

        $table .= '
<script type="text/javascript">
var ' . $tname . '_invalid=true; var ' . $tname . ';
$(document).ready(function() {
    $(\'#' . $table_id . ' tfoot th\').each(function() {
    	var c = $(this).attr("class"); if(c!=undefined && c!="" && c.indexOf("mSearch") != -1) {
    	var title = $(\'#' . $table_id . ' thead th\').eq( $(this).index() ).text();
    	if(title.toLowerCase() != \'actions\') {
    		$(this).html( \'<input class="form-control input-sm" type="text" placeholder="Filter \'+title+\'" data-toggle="tooltip" data-original-title="Filter \'+title+\'" />\' )
    				.keypress( function() { if(' . $tname . '_invalid) {  ' . $tname . '.rows().invalidate().draw(); ' . $tname . '_invalid=true; } });;
    		} else { $(this).html(""); }
    	} else { $(this).html(""); }
    } );
    ' . $tname . '= $(\'#' . $table_id . '\').DataTable({' . $buttons . ' oLanguage: {sSearch: "Search all columns"},"pagingType": "full_numbers", '
        . ($this->_enableShowAll ? ' "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]], ' : '')
        . ' sDom: \'' . $dom . 'r<"top"' . ($this->_gridSearch ? 'f' : '') . '>rt<"bottom"lip><"clear">\',
    	initComplete: function() { var r = $(\'#' . $table_id . ' tfoot tr\'); r.find(\'th\').each(function() { $(this).css(\'padding\', 9); });
    	$(\'#' . $table_id . ' thead\').append(r); $(\'#search_0\').css(\'text-align\', \'center\'); },
    	aoColumns:' . json_encode($dt_js_columns)
        . ($this->_defaultSortCol ? ', order:[["' . $this->_defaultSortCol . '", "' . ($this->_defaultSortDir ? $this->_defaultSortDir : "asc") . '" ]]' : "")
        . ' });
    	' . $tname . '.columns().eq(0).each( function (colIdx) { $( \'input\', ' . $tname . '.column(colIdx).footer()).on(\'keyup change\',
    	function() { ' . $tname . '.column( colIdx ).search( this.value ).draw(); } );
    } );
	$(\'#' . $table_id . '_filter input\').attr("class", "form-control input-sm").keypress( function() { if(' . $tname . '_invalid) {  ' . $tname . '.rows().invalidate().draw(); ' . $tname . '_invalid=true; } });
/*	$(".dt-buttons > .dt-button").each(function(){ $(this).attr(\'class\',\'btn btn-default md-margin\')});	$(".paginate_button").each(function(){ $(this).attr(\'class\',\'btn btn-default btn-xs md-margin\')});*/
});
function editRow(itemId) { event.preventDefault();
    var newForm = jQuery(\'<form>\', { \'action\': \''
        . (substr($this->_baseUrl, 0 - strlen('entry')) == 'entry' ? $this->_baseUrl : $this->_baseUrl . '/entry') . '\''
        . ', \'method\': \'POST\'})
        .append(jQuery(\'<input>\', {\'name\': \'q\', \'value\': itemId, \'type\': \'hidden\'}))
        .append(jQuery(\'<input>\', {\'name\': "' . $csrf['name'] . '", \'value\':"' . $csrf['hash'] . '", \'type\': \'hidden\'}));
    $(document.body).append(newForm);
    newForm.submit();
}
function deleteRow(itemId) { event.preventDefault();
    var r = confirm(\'Are you sure you want to delete this record\');
    if(r) {
        var newForm = jQuery(\'<form>\', { \'action\': \'' . $this->_baseUrl . '/delete\', \'method\': \'POST\'})
        .append(jQuery(\'<input>\', {\'name\': \'q\', \'value\': itemId, \'type\': \'hidden\'}))
        .append(jQuery(\'<input>\', {\'name\': "' . $csrf['name'] . '", \'value\':"' . $csrf['hash'] . '", \'type\': \'hidden\'}));
        $(document.body).append(newForm);
        newForm.submit();
    } else { return false; }
}
</script>
<style> .dt-buttons>.dt-button, .paginate_button, a.paginate_button  {margin: 9px; display: inline-block;  margin-bottom: 0;   text-align: center; white-space: nowrap; vertical-align: middle;  -ms-touch-action: manipulation; touch-action: manipulation;  cursor: pointer; -webkit-user-select: none; -moz-user-select: none; -ms-user-select: none;  user-select: none;
	background-image: none; border: 1px solid transparent;  border-radius: 5px; padding: 2px 5px; text-decoration:none;
	font-size: 12px; line-height: 1.5; font-weight:normal; color: #333333!important; background-color: #fff;  border-color: #ccc;}
	.current, a.current {font-size:14px!important; font-weight:bold!important; padding:9px;}
</style> ';

        $retval = array(
            "table_id" => $table_id,
            $this->_table_view_id => $table,
            "totnumofrows_$table_id" => $numofrows,
        );
        return $retval;
    }

    public function set_columns_attribute($col)
    {
        $attribute = array();
        //$attribute ["mDataProp"] = $col ["header"];
        $attribute["bVisible"] = $col["is_visisble"];
        $attribute["bSortable"] = $col["is_sortable"];
        $attribute["bSearchable"] = $col["is_searchable"];
        $attribute["sClass"] = $col["class"];
        //$attribute ["sType"] = $col ["field_type"];
        // $attribute["sTitle"] = $col["header"];
        if ($col["misc"]) {
            foreach ($col["misc"] as $key => $val) {
                $attribute[$key] = $val;
            }
        }
        //    var_dump($attribute); die();

        return $attribute;
    }

    public function decode_id($q, $dt_format = 'Y-m-d H:i:s')
    {
        $ret_array = array();
        $q = json_decode(base64_decode($q));
        foreach ($q as $key => $val) {
            if ($key != 'dt') {
                $ret_array[$key] = json_decode(base64_decode($val));

            } else {
                $ret_array['dt'] = date($dt_format);
            }
        }
        return $ret_array;
    }

    public function encode_id($arr, $dt = null )
    {
        if(!isset($dt)) {
            $dt = date('U');
        }
        $tr_id = base64_encode(json_encode($arr));
        return  base64_encode(json_encode(array('id'=>$tr_id, 'dt'=> $dt)));
    }

    /***************************************** 2019 ****************************************************/
    ////GENERATORS

    public function common_display_view($view, $data, $title, $id = "", $subheading = "", $menu = "")
    {
        $CI = &get_instance();
        $data["title"] = $title;
        $data["heading"] = "ARD";
        $data["subheading"] = $subheading ? $subheading : $title;
        $data["menu"] = $menu ? $menu : $title;
        ///var_dump( ($data) ); die();
        $data["content"] = $CI->parser->parse($view, ($data), true);
        $CI->parser->parse('../../templates/template.php', $data);
    }

    public function get_entry_fields($data, $form_post_url, $suffix=null, $returl=null, $setupload=null)
    {
        ///var_dump($data); die();
        $return_str = form_open($form_post_url, array('id'=>'frm' . (isset($suffix) ? "_" . $suffix : "" )));
        // echo 'here '. ($setupload ? 'true' : 'false');
        if ($setupload) {
            // echo 'form_upload'; 
            $return_str = form_open_multipart($form_post_url, array('id'=>'frm' . (isset($suffix) ? "_" . $suffix : "" ), 'autocomplete'=>"no"));
        }
        
        foreach ($data as $key => $d) {
            // if($key=='label') continue;
            if (isset($d["label"]) && $d["label"] != '') {
                $fld_name = $d["label"];
            } else {
                $fld_name = ucwords(str_replace("_", " ", $key));
            }
            switch (strtolower($d['type'])) {
                case 'file':
                    $return_str .= '<div class="form-group"><label class="form-label" for="' . $key . '" id="lbl_' . $key .'" >' . $fld_name . '</label>'
                        . '<input class="form-control" name="' . $key . '"';
                    foreach ($d as $attr => $val) {
                        if($attr == 'label') continue;
                        $return_str .= $attr . '="' . $val . '"';
                    }
                    $return_str .= '>';
                    if($d['value']) {
                        $return_str .= '<img src="'. base_url( $d['value'] ).'" width="120em"/>';
                    }
                    $return_str .= '</div>';
                break;
                case 'hidden':
                    $return_str .= '<input type="' . $d["type"] . '" ' . (isset($d["id"]) ? 'id="'.$d['id']. '"' : '') . ' name="' . $key . '" value="' . $d["value"] . '">';
                    break;
                case 'text':
                case 'number':
                case 'tel':
                case 'date':
                case 'password':
                case 'email':
                    $return_str .= '<div class="form-group"><label class="form-label" for="' . $key . '" id="lbl_' . $key .'" >' . $fld_name . '</label>'
                        . '<input class="form-control" name="' . $key . '"';
                    foreach ($d as $attr => $val) {
                        if($attr == 'label') continue;
                        $return_str .= $attr . '="' . $val . '"';
                    }
                    $return_str .= '> </div>';
                    break;
                case 'select':
                    $return_str .= '<div class="form-group"><label class="form-label" for="' . $key . '" id="lbl_' . $key .'" >' . trim(str_replace('Id', '', $fld_name)) . '</label>'
                        . '<select class="form-control" name="' . $key . '"';
                    foreach ($d as $attr => $val) {
                        if ($attr != 'options') {
                            $return_str .= $attr . '="' . $val . '"';
                        }
                    }
                    $return_str .= '>';
					$selected = false;
					// echo 'KEY'.$d['key'];
                    foreach ($d["options"] as $opt) {
                    	// echo "<br/>";
                    	$return_str .= '<option value="' . $opt['val'] . '" ' . (((@$opt['disabled'] == "") && !$selected && $d['key'] == $opt['val']) ? ' selected' : '');
                        foreach ($opt as $opt_key => $opt_value) {
                            if($opt_value!=null && $opt_key != 'val' && $opt_key != 'name') {
                                $return_str .= " " . $opt_key . '="' . $opt_value . '"';
                            }
                        }
                        $return_str .= '>' . $opt['name'] . '</option>';
                        $selected = $d['key'] == $opt['val'];
                    }
                    $return_str .= '</select></div>';
                    
                    break;
                case 'checkbox':
                    $return_str .= '<div><input type="checkbox" value="' . $d["value"] . '" name="' . $d["name"] . '" ';
                    $return_str .= isset($d['checked']) && $d['checked'] == true ? ' checked="checked" ' : '';
                    $return_str .= '> ' . $fld_name . '</div>';
                    break;
                case 'radio': 
                    $return_str .= '<div class="form-group"><label class="form-label" for="' . $key . '" id="lbl_' . $key .'" >' . trim(str_replace('Id', '', $fld_name)) . '</label>';
                    foreach ($d["options"] as $opt) {
                        $return_str .= '<input type="radio" name="' . $key . '" value="' . $opt['val'] . '"/>';
                    }
                    $return_str .= '</div>';
                    break;
            }
        }
        return $return_str . $this->get_entry_buttons($suffix,$returl) . form_close();
    }
    public function get_entry_buttons($suffix = null, $returl=null)
    {
        if(!isset($returl)) { 
            $returl = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : base_url("");
        }
        $return_str = '<div class="form-group text-center"><button id="btnCancel' . (isset($suffix) ? "_" . $suffix : "") . '" type="button" class="btn btn-warning" onclick="window.location.href=\''
            . $returl . '\';return false;">Cancel</button>'
            . ' <button id="btnSave' . (isset($suffix) ? "_" . $suffix : "") . '" type="submit" class="btn btn-primary">Save</button></div>';

        return $return_str;
    }

    public function check_value($evalue, $rvalue = '')
    {
        return $evalue != null && isset($evalue) && !empty($evalue) ? $evalue : $rvalue;
    }

    public function redirect($redirect_to_url)
    {
        if (!headers_sent()) {
            header('Location:' . $redirect_to_url);
        } else {
            echo '<script>window.location.href="' . $redirect_to_url . '";</script>';
        }

    }

    ////GENERATORS

    /***************************************** 2019 ****************************************************/

}
