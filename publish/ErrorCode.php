<?php

declare(strict_types=1);
/**
 * This file is part of Hyperf.
 *
 * @link     https://www.hyperf.io
 * @document https://doc.hyperf.io
 * @contact  group@hyperf.io
 * @license  https://github.com/hyperf/hyperf/blob/master/LICENSE
 */
namespace App\Constants;

use Hyperf\Constants\AbstractConstants;
use Hyperf\Constants\Annotation\Constants;

/**
 * @method static getMessage(int $code)
 * @Constants
 */
class ErrorCode extends AbstractConstants
{
    /**
     * @Message("success")
     */
    const SUCCESS = 0;

    /**
     * @Message("认证失败")
     */
    const AUTH_FAILURE = 401;

    /**
     * @Message("无访问权限")
     */
    const PERMISSION_REFUSE = 403;

    /**
     * @Message("数据不存在")
     */
    const NOT_FOUND = 404;

    /**
     * @Message("表单参数验证失败")
     */
    const PARAMETER_ERROR = 422;
    /**
     * @Message("表单参数认证失败")
     */
    const PARAMETER_UNAUTH = 425;

    /**
     * @Message("Server Error！")
     */
    const SERVER_ERROR = 500;

    /**
     * @Message("操作失败")
     */
    const OPERATE_FAILURE = 555;
}
