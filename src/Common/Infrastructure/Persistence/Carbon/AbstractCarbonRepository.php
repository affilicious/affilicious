<?php
namespace Affilicious\Common\Infrastructure\Persistence\Carbon;

use Affilicious\Common\Infrastructure\Persistence\Wordpress\AbstractWordpressRepository;

if(!defined('ABSPATH')) {
    exit('Not allowed to access pages directly.');
}

abstract class AbstractCarbonRepository extends AbstractWordpressRepository
{
}
