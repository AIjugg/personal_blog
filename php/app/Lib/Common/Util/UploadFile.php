<?php
/**
 * Created by PhpStorm.
 * User: zhanchenchang
 * Date: 2023-09-02
 * Time: 21:02
 */

namespace App\Lib\Common\Util;


class UploadFile
{
    protected $imgPath = '/images/';


    /**
     * 图片上传功能
     * @param $base64
     * @param $type
     * @return array
     * @throws CommonException
     */
    public function uploadImg($base64, $type)
    {
        if (empty($base64)) {
            throw new CommonException(ErrorCodes::IMAGE_UPLOAD_EMPTY);
        }
        //简单判断是否为base64
        if (strpos($base64, 'data:image/') === false) {
            throw new CommonException(ErrorCodes::IMAGE_FORMAT_WRONG);
        }
        $imgBase64 = str_replace('data:image/jpeg;base64,', '' ,$base64);
        $imgBase64 = str_replace('data:image/png;base64,', '' ,$imgBase64);
        $imgBase64 = str_replace('=', '', $imgBase64);
        $imgLength = strlen($imgBase64);
        $imgSize = $imgLength - ($imgLength / 8) * 2;
        $imgSize = number_format(($imgSize / 1024), 2) . 'kb';
        //理解一下base64的编码方式，是把3个8字节编码成4个6字节，到这一步字节数是不变的
        //但它还要在6个字节添加两个高位组成4个8字节，base64有多少个8字节，就比原来多2倍的多少个8字节
        //也就是base64长度要比原码长度多了（base64长度/8）*2个字节，换算过来就是要减掉
        if ($imgSize > (1024 * 1024 * 2)) {
            throw new CommonException(ErrorCodes::IMAGE_TOO_LARGE);
        }

        $randomCode = date('YmdHis') . GenerateRandom::generateUnique(1);
        switch ($type) {
            case 'profilePhoto':
                {
                    $imgName = 'profileImg/' . $randomCode;
                    break;
                }
            case 'cover':
                {
                    $imgName = 'cover/' . $randomCode;
                    break;
                }
            case 'blog':
                {
                    $imgName = 'blog/' . $randomCode;
                    break;
                }
            default:
                {
                    $imgName = 'other/' . $randomCode;
                }
        }
        $imgPath = $this->imgPath . $imgName . '.png';
        $res = file_put_contents(dirname(base_path()) . $imgPath, base64_decode($imgBase64));
        if ($res) {
            return ['imgPath' => $imgPath];
        } else {
            throw new CommonException(ErrorCodes::IMAGE_UPLOAD_FAIL);
        }
    }
}
