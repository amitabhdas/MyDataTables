# MyDataTables
PHP wrapper for datatables (http://www.datatables.net/), and code generator, can be used with CodeIgniter or any other framework. 
Supports both Angular and normal JS

LIST GENERATOR


        $this->mydatatables->setTitle("LIST USERS");
        $this->mydatatables->setTableName("vwUsers"); // can be a table or view
        $this->mydatatables->setOrder('1 desc');  // Comma separated order by clause

        $this->mydatatables->setWhere($where);  /* where clause can be an associative array OR string array('user_id'=>1, 'name'=>'Amitabh
        ) */

        $this->mydatatables->setId("{id}"); /* Primary key / Unique key which can be used to uniquely identify the row */
        /* addColumn($field, $field_type="html", $header="", $class="", $is_sortable=true, $is_searchable=true, $is_visisble=true, $misc=null) */
        $this->mydatatables->addColumn('{user_id}', "html", '', "text-right", false, false, false);

        $this->mydatatables->addColumn("{first_name} {middle_name} {last_name}<br/>{designation}", "html", "Name", "", true, true, true);
        $this->mydatatables->addColumn('{gender}', "html", 'Gender<br/><small>(M=Male, F=Female, O=Others)</small>', "text-center", true, true, true);
        $this->mydatatables->addColumn('{dob:dt:d-M-Y}', "html", 'Date of Birth', "text-right", true, true, true);
        $this->mydatatables->addColumn('{email}', "html", 'Email', "text-right", true, true, true);
        
        $this->mydatatables->setAllowAdd(false); /// default true the Add button wont appear; comment if you want add button for CRUD
        $this->mydatatables->addColumn('{username}', "html", 'Login', "text-right", true, true, true)
        
        
        /// ADD Multiple buttons for each type of acctions 
        /// addAction($text, $link, $class = "", $icon = "", $angular = false)
        $this->mydatatables->addAction("Edit", "comm_sub('{id:en}')", "", "fa fa-pencil", false); 
        $this->mydatatables->addAction("Delete", "comm_sub('{id:en}');", "", "fa fa-close", false);
   
        /// URL for CRUD 
        $this->mydatatables->SetBaseUrl($this->BASEURL . '/user_entry');
        
        /// FINAL HTML GENERATION which is stored in a var which can be echoed or sent to view (codeigniter)
        $datatable = $this->mydatatables->generate();
        
        
MORE ON CRUD FIELD GENERATOR TO COME
        
