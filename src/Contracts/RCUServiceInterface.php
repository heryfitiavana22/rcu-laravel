<?php

namespace RCU\Contracts;

interface RCUServiceInterface
{
    public function uploadStatus($query);
    public function upload($dto);
}
