<?php
namespace lib\db;
    use core\Lib;

    class Select extends Lib{
        var $_table;
        var $_fields=array();
        var $_joins=array();
        var $_where=array();
        var $_order=array();
        var $_group=array();
        var $_limit=0;
        var $_offset=0;
        var $_ignoreParent=false;
        var $executed=null;


        function __construct(){

        }


        function ignoreParent(){
            $this->_ignoreParent=true;
            return $this;
        }
        function table($table,$alias=false){
            if(!is_array($table)) {
                if (!$alias) {
                    $alias = $table;
                }
                $table=array($alias=>$table);
            }
                $this->table =$table;
            return $this;


        }

        function field($fields){
            if(is_array($fields)){
                foreach($fields as $alias=>$field){
                    if(is_numeric($alias)){
                        $alias=$field;
                    }
                    $this->_fields[$alias]=$field;
                }
            }else{
                $this->_fields[$fields]=array($fields=>$fields);
            }

            return $this;
        }

        function join($table,$onLeft,$onRight,$alias=false){

            if(!is_array($table)){
                if(!$alias){
                    $alias=$table;
                }
                $table=array($table=>$alias);
            }
            $this->_joins[]=array(
                "table"=>$table,
                "left"=>$this->getFullFieldName($onLeft,$alias),
                "right"=>$this->getFullFieldName($onRight,$alias),
            );

            return $this;
        }

        function where($condition){
            $this->_where[]=$condition;

            return $this;
        }

        function orderBy($fields){
            if(is_array($fields)){
                foreach($fields as $field=>$order){
                    if(is_numeric($field)){
                        $field=$order;
                        $order="asc";
                    }
                    $this->_orderBy[$field]=$order;
                }
            }else{
                $this->_orderBy[$fields]="asc";
            }

            return $this;
        }

        function group($fields){
            if(is_array($fields)){
                foreach($fields as $field=>$order){
                    if(is_numeric($field)){
                        $field=$order;
                        $order="asc";
                    }
                    $this->_group[$field]=$order;
                }
            }else{
                $this->_group[$fields]="asc";
            }

            return $this;
        }

        function limit($max,$offset){
            $this->_limit=$max;
            $this->_offset=$offset;

            return $this;
        }

        function execute(){
            $queryString="select ";

            //fields
            if(count($this->_fields)){
                $i=0;
                foreach($this->_fields as $alias=>$field){
                    if($i){
                        $queryString.=" , ";
                    }
                    $queryString.="`".$field."`";
                    if($alias!=$field){
                        $queryString.=" as ".$alias;
                    }
                    $i++;
                }
            }else{
                $queryString.=" * ";
            }


            $table=array_keys($this->table)[0];
            $alias=array_values($this->table)[0];
            $queryString.=" from ".$table;
            if($table!=$alias){
                $queryString.=" as `".$alias."`";
            }



            foreach($this->_joins as $join){
                $queryString.=" left join ";
                $table=array_keys($join["table"])[0];
                $alias=array_values($join["table"])[0];
                $queryString.="`".$table."`";
                if($alias!=$table){
                    $queryString.=" as ".$alias;
                }
                $queryString.=" on ".$join["left"]." = ".$$join["right"];
            }

            if(count($this->_where)){
                $queryString.=" where (";
                $i=0;
                foreach($this->_where as $where){
                    if($i){
                        $queryString.=")and(";
                    }
                    $queryString.=$where;
                    $i++;
                }
                $queryString.=")";
            }

            if(count($this->_order)){
                $queryString.=" order by ";
                $i=0;
                foreach($this->_order as $orderby=>$order){
                    if($i){
                        $queryString.=" , ";
                    }
                    $queryString.=$orderby." ".$order;
                    $i++;
                }
            }

            if(count($this->_group)){
                $queryString.=" order by ";
                $i=0;
                foreach($this->_group as $groupby=>$order){
                    if($i){
                        $queryString.=" , ";
                    }
                    $queryString.=" ".$groupby." ".$order;
                    $i++;
                }
            }

            if($this->limit){
                $queryString.=" limit ";
                if($this->_offset){
                    $queryString.=$this->_offset.",";
                }
                $queryString.=" ".$this->_limit;
            }

            $this->executed=$this->db->query($queryString);
        }



        function fetchAll()
        {
            if($this->executed===null){
                $this->execute();
            }
            return $this->executed->fetchAll();
        }

        function fetch()
        {
            if($this->executed===null){
                $this->execute();
            }
            return $this->executed->fetch();
        }

        function fetchModel($class, $namespace = false)
        {
            if($this->executed===null){
                $this->execute();
            }
            return $this->executed->fetchModel($class,$namespace);
        }

        function fetchAllModel($class, $namespace=false)
        {
            if($this->executed===null){
                $this->execute();
            }
            return $this->executed->fetchAllModel($class,$namespace);
        }

        function numRows()
        {
            if($this->executed===null){
                $this->execute();
            }
            return $this->executed->numRows();
        }


    }
?>