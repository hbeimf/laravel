<?php
/**
 * swagger 接口文档说明
 *
 * Created by PhpStorm.
 * User: xjc
 * Date: 2018/7/19
 * Time: 15:06
 */

/**
 * host="api.22dutech.com",
 * host="web.laravel.com",
 *
 * @SWG\Swagger(
 *     schemes={"http"},
 *     @SWG\SecurityScheme(
 *         securityDefinition="api-token",
 *         type="apiKey",
 *         name="Authorization",
 *         in="header"
 *     ),
 *     host="api.22dutech.com",
 *     produces={"application/json"},
 *     consumes={"multipart/form-data"},
 *     @SWG\Info(
 *         version="1.0.0",
 *         title="测试项目",
 *         description="一个项目的接口文档
 * 需要登录的地方加header头 Accept:application/json; Authorization:Bearer+空格+token",
 *     ),
 *     @SWG\Definition(
 *         definition="ErrorModel",
 *         type="object",
 *         required={"code", "message"},
 *         @SWG\Property(
 *             property="code",
 *             type="integer",
 *             format="int1",
 *             description="200:成功；"
 *         ),
 *         @SWG\Property(
 *             property="msg",
 *             type="string",
 *             description="错误信息"
 *         )
 *     ),
 *     @SWG\Definition(
 *         definition="Error401",
 *         type="object",
 *         required={"message"},
 *         description="用户授权错误，场景:未登录就访问资源， 服务器返回401状态码",
 *         @SWG\Property(
 *             property="message",
 *             type="string",
 *             description="用户未授权",
 *         ),
 *         @SWG\Property(
 *             property="status_code",
 *             type="integer",
 *             description="返回的状态码401",
 *         )
 *     ),
 *        @SWG\Definition(
 *         definition="Error400",
 *         type="object",
 *         required={"message"},
 *         description="数据校验错误，服务器返回400状态码",
 *         @SWG\Property(
 *             property="message",
 *             type="string",
 *             description="错误描述信息,例如：请输入有效的邮箱地址",
 *         ),
 *         @SWG\Property(
 *             property="status_code",
 *             type="integer",
 *             description="返回的状态码400",
 *         )
 *     ),
 *         @SWG\Definition(
 *         definition="Error403",
 *         type="object",
 *         required={"message"},
 *         description="用户访问没有权限的资源服务器返回403状态码",
 *         @SWG\Property(
 *             property="message",
 *             type="string",
 *             description="错误描述信息,例如：权限不足",
 *         ),
 *         @SWG\Property(
 *             property="status_code",
 *             type="integer",
 *             description="返回的状态码403",
 *         )
 *     ),
 *     @SWG\Definition(
 *         definition="Error404",
 *         type="object",
 *         required={"message"},
 *         description="没有找到资源返回404状态码",
 *         @SWG\Property(
 *             property="message",
 *             type="string",
 *             description="错误描述信息,例如：该资源未找到",
 *         ),
 *         @SWG\Property(
 *             property="status_code",
 *             type="integer",
 *             description="返回的状态码404",
 *         )
 *     ),
 *     @SWG\Tag(
 *          name="api-user",
 *          description="前台用户操作",
 *     ),

 * )
 */