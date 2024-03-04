<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class LogFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return mixed
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        $db = \Config\Database::connect();
        $data = [
            'type' => 'web',
            'scheme' => $request->getServer('REQUEST_SCHEME') ?? '',
            'host' => $request->getServer('HTTP_HOST') ?? '',
            'path' => $request->getServer('PATH_INFO') ?? '',
            'method' => $request->getMethod() ?? '',
            'query_string' => $request->getServer('QUERY_STRING') ?? '',
            'data' => $request->getBody() != null ? json_encode($request->getBody()) : '',
            'content_type' => $request->getHeaderLine('Content-Type'),
            'remote_addr' => $request->getServer('SERVER_ADDR'),
            'server_addr' => $request->getIPAddress(),
            'nickname' => auth()->user()->nickname ?? ''
        ];

        if ($request->isCLI())
        {
            $data['type'] = 'command';
            $data['command'] = implode(' ', $_SERVER['argv']);
        }

        $db->table('zenith_logs')->insert($data);
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return mixed
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
