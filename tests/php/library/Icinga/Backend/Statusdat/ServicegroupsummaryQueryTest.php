<?php

namespace Tests\Icinga\Backend\Statusdat;
use Tests\Icinga\Protocol\Statusdat\ReaderMock as ReaderMock;
require_once("Zend/Config.php");
require_once("./library/Icinga/Protocol/Statusdat/ReaderMock.php");
require_once("../../library/Icinga/Backend/Query.php");
require_once("../../library/Icinga/Backend/Criteria/Order.php");
require_once("../../library/Icinga/Backend/AbstractBackend.php");
require_once("../../library/Icinga/Backend/Statusdat/Query.php");
require_once("../../library/Icinga/Backend/Statusdat/GroupsummaryQuery.php");
require_once("../../library/Icinga/Backend/Statusdat/ServicegroupsummaryQuery.php");

class BackendMock  extends \Icinga\Backend\AbstractBackend{
    public $reader;

    public function select() {
        return $this;
    }
    public function setReader($reader) {
        $this->reader = $reader;
    }

    public function getReader() {
        return $this->reader;
    }
}


/**
*
* Test class for Servicegroupsummaryquery 
* Created Mon, 18 Feb 2013 14:33:21 +0000 
*
**/
class ServicegroupsummaryqueryTest extends \PHPUnit_Framework_TestCase
{

    public function testGroupByProblemType()
    {
        $backend = new BackendMock();
        $backend->setReader($this->getTestDataset());
        $q = new \Icinga\Backend\Statusdat\ServicegroupsummaryQuery($backend);
        $indices = array(
            "service" => array(
                "hosta;service1", "hosta;service2", "hosta;service3",
                "hostb;service1", "hostb;service2", "hostb;service3", "hostb;service4"
            )
        );
        $this->assertEquals(array(
            (object) array(
                "servicegroup_name" => "sv1",
                'ok'           => 1,
                'critical'     => 1,
                'critical_dt'  => 0,
                'critical_ack' => 1,
                'unknown'      => 0,
                'unknown_dt'   => 0,
                'unknown_ack'  => 0,
                'warning'      => 0,
                'warning_dt'   => 1,
                'warning_ack'  => 2
            ),
            (object) array(
                "servicegroup_name" => "sv2",
                'ok'           => 0,
                'critical'     => 0,
                'critical_dt'  => 0,
                'critical_ack' => 1,
                'unknown'      => 0,
                'unknown_dt'   => 0,
                'unknown_ack'  => 0,
                'warning'      => 1,
                'warning_dt'   => 0,
                'warning_ack'  => 2
            )
        ),$q->groupByProblemType($indices));
    }



    private function getTestDataset()
    {
        return new ReaderMock(array(
            "host" => array(
                "hosta" => (object) array(
                    "host_name" => "hosta",
                    "numeric_val" => 0,
                    "services" => array(0, 1, 2)
                ),
                "hostb" => (object) array(
                    "host_name" => "hostb",
                    "numeric_val" => 0,
                    "services" => array(3, 4, 5)
                )
            ),
            "service" => array(
                "hosta;service1" => (object) array(
                    "host_name" => "hosta",
                    "service_description" => "service1",
                    "group" => array(
                        "sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 0,
                        "problem_has_been_acknowledged" => 0

                    )
                ),
                "hosta;service2" => (object) array(
                    "host_name" => "hosta",
                    "service_description" => "service2",
                    "group" => array(
                        "sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 1,
                        "downtime" => array("..."),
                        "problem_has_been_acknowledged" => 0
                    )
                ),
                "hosta;service3" => (object) array(
                    "host_name" => "hosta",
                    "service_description" => "service3",
                    "group" => array(
                        "sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 2,
                        "problem_has_been_acknowledged" => 0
                    )
                ),
                "hostb;service1" => (object) array(
                    "host_name" => "hostb",
                    "service_description" => "service1",
                    "group" => array(
                        "sv2"
                    ),
                    "status" => (object) array(
                        "current_state" => 1,
                        "problem_has_been_acknowledged" => 0
                    )
                ),
                "hostb;service2" => (object) array(
                    "host_name" => "hostb",
                    "service_description" => "service2",
                    "group" => array(
                        "sv2","sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 2,
                        "problem_has_been_acknowledged" => 1
                    )
                ),
                "hostb;service3" => (object) array(
                    "host_name" => "hostb",
                    "service_description" => "service3",
                    "group" => array(
                        "sv2","sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 1,
                        "problem_has_been_acknowledged" => 1
                    )
                ),
                "hostb;service4" => (object) array(
                    "host_name" => "hostb",
                    "service_description" => "service4",
                    "group" => array(
                        "sv2","sv1"
                    ),
                    "status" => (object) array(
                        "current_state" => 1,
                        "problem_has_been_acknowledged" => 1
                    )
                )
            )
        ));
    }
}

