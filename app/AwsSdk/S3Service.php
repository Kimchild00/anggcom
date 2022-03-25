<?php

namespace App\AwsSdk;

use Aws;

class S3Service{
    protected $s3, $bucket;

    public function __construct() {
        $this->s3 = new Aws\S3\S3Client([
            'version' => 'latest',
            'region'  => 'ap-southeast-1',
            'debug'   => false,
            'credentials' => [
                'key'    => env('AWS_KEY_ID', false),
                'secret' => env('AWS_ACCESS_KEY', false)
            ]
        ]);
        $this->bucket   = 'importir';
    }

    public function putObject($fileObject, $name = 'no-name.jpg', $getMime = "image/jpeg", $source = 'images', $multiple = false){
        $arr    = explode(".", $name);
        if (count($arr) < 2) {
            return false;
        }

        if (!$multiple) {
            $name   = str_slug($arr[0]) . '-' . date("Y_m_d_H_i_s") . '.' . $arr[1];
        }

        $props  = [
            'Bucket'        => $this->bucket,
            'Key'           => $source . '/' . $name,
            'SourceFile'    => $fileObject,
            'ContentType'   => $getMime,
            'ACL'           => 'public-read'
        ];

        try {
            $this->s3->putObject($props);
            $result = [
                'name'      => $name,
                'source'    => $source
            ];
        } catch (Aws\S3\Exception\S3Exception $exception){
            $result = false;
        }

        return $result;
    }
}