<?php

namespace Saya\Core;

use Saya\Core\IRC\Command;
use Saya\Core\IRC\Errors;
use Saya\Core\IRC\Response;

interface IRC extends Command, Errors, Response
{
}
