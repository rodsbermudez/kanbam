<?php

namespace App\Models;

use CodeIgniter\Model;

class SlackChannelLogModel extends Model
{
    protected $table            = 'slack_channels_log';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = ['project_id', 'slack_channel_id', 'slack_channel_name'];

    protected $useTimestamps    = true;
}