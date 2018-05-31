<?php
namespace framework\provider\search;

use framework\lib\provider\baseprovider\BaseSearchProvider;

class Db extends BaseSearchProvider
{
    function search($query, $keywords, $fields, $useScores)
    {
        if ($useScores) {
            $query->ignorePagination();
        }
        $this->createWhere($query, $keywords, $fields);
        $results = $query->fetchAll();
        if($useScores){
          $results=  $this->handleScores($results,$keywords,$fields);
        }
        return $results;
    }

    function handleScores($results,$keywords,$fields){
        $count=count($results);

        foreach ($results as &$result){
            $score=0;

            foreach($fields as $field) {
                foreach ($keywords["optional"] as $keyword) {
                    $score+=substr_count(strtolower($result[$field["name"]]),strtolower($keyword))*$field["score"];
                    $score+=substr_count(strtolower(" ".$result[$field["name"]]),strtolower($keyword))*$field["score"]*2;
                    $score+=substr_count(strtolower($result[$field["name"]]." "),strtolower($keyword))*$field["score"]*4;
                    $score+=substr_count(strtolower(" ".$result[$field["name"]]." "),strtolower($keyword))*$field["score"]*8;

                }

                foreach ($keywords["required"] as $keyword) {
                    $score+=substr_count(strtolower($result[$field["name"]]),strtolower($keyword))*$field["score"]*2;
                    $score+=substr_count(strtolower(" ".$result[$field["name"]]),strtolower($keyword))*$field["score"]*4;
                    $score+=substr_count(strtolower($result[$field["name"]]." "),strtolower($keyword))*$field["score"]*8;
                    $score+=substr_count(strtolower(" ".$result[$field["name"]]." "),strtolower($keyword))*$field["score"]*16;
                }
            }
            $result["__score"]=$score;
        }

        usort($results, function ($a, $b) {
            if (version_compare(PHP_VERSION, '7.0.0') >= 0) {
                return $b['__score'] <=> $a['__score'];
            }else{
                return $b['__score'] - $a['__score'];
            }
        });

        $results=array_values($results);
        $pagination=$this->db->paginationInfo();
        $pagination->setResultCount($count);

        $limit=explode(",",$pagination->getLimit());
        return array_slice($results,$limit[0],$limit[1]);




    }

    function createWhere($query, $keywords, $fields)
    {
        $query->where(function ($condition) use ($query, $keywords, $fields) {

            //optional keywords
            if($keywords["optional"] || $keywords["required"]) {
                $condition->sub(function ($condition) use ($keywords, $fields) {
                    $i = 0;
                    foreach ($keywords["optional"] as $kKey => $keyword) {
                        if ($i) {
                            $condition->bOr();
                        }

                        foreach ($fields as $key => $field) {
                            if ($key) {
                                $condition->bor();
                            }
                            $condition->like($field["name"], "'%" . $keyword . "%'");

                        }
                        $i++;
                    }


                    foreach ($keywords["required"] as $kKey => $keyword) {
                        if ($i) {
                            $condition->bOr();
                        }

                        foreach ($fields as $key => $field) {
                            if ($key) {
                                $condition->bor();
                            }
                            $condition->like($field["name"], "'%" . $keyword . "%'");

                        }
                        $i++;
                    }
                });
            }

            //required keywords
            if (count($keywords["optional"]) && $keywords["required"]) {
                $condition->bAnd();
            }
            $i = 0;

                foreach ($keywords["required"] as $key => $keyword) {
                    if ($i) {
                        $condition->bAnd();
                    }
                    $condition->sub(function ($condition) use ($keyword, $fields) {
                        foreach ($fields as $key => $field) {
                            if ($key) {
                                $condition->bor();
                            }
                            $condition->like($field["name"], "'%" . $keyword . "%'");

                        }
                    });
                    $i++;
                }

            //ignored or filtered out keywords
            if ((count($keywords["optional"]) || count($keywords["required"])) && $keywords["ignore"]) {
                $condition->bAnd();
            }

            if($keywords["ignore"]) {
                $condition->sub(function ($condition) use ($keywords, $fields) {
                    foreach ($keywords["ignore"] as $key => $keyword) {
                        foreach ($fields as $key => $field) {
                            if ($key) {
                                $condition->bAnd();
                            }
                            $condition->sub(function ($condition) use ($keyword,$field) {
                                $condition->notLike($field["name"], "'%" . $keyword . "%'");
                                $condition->bOr();
                                $condition->isNull($field["name"]);
                            });

                        }
                        $i++;
                    }
                });
            }


        });
    }
}