<?php

namespace JoeBengalen\SlimCompress;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\StreamInterface;
use Slim\Http\Body;

/**
 * CompressionMiddleware.
 *
 * If encoding type gzip is supported or if specificly asked for the .gz
 * extension this middleware will replace the body content with the gzip encoded
 * string. Also the content encoding header will be added.
 */
class GzipCompressionMiddleware
{
    /**
     * @var string
     */
    protected $extension = '.gz';

    /**
     * @var string
     */
    protected $acceptEncodingHeader = 'gzip';

    /**
     * @var int
     */
    protected $compressionLevel = 6;

    /**
     * @var bool
     */
    protected $supportExtension;

    /**
     * @var bool
     */
    protected $supportAcceptEncodingHeader;

    /**
     * @var bool
     */
    protected $useCompression = false;

    /**
     * Create CompressMiddleware.
     *
     * @param bool $supportExtension
     * @param bool $supportAcceptEncodingHeader
     */
    public function __construct(
        $supportExtension = true,
        $supportAcceptEncodingHeader = true
    ) {
        $this->supportExtension = $supportExtension;
        $this->supportAcceptEncodingHeader = $supportAcceptEncodingHeader;
    }

    /**
     * Body factory.
     *
     * @return StreamInterface
     */
    protected function createBody()
    {
        return new Body(fopen('php://temp', 'r+'));
    }

    /**
     * Compress the Response.
     *
     * @param Response $response
     *
     * @return Response
     */
    protected function compress(Response $response)
    {
        $body = $response->getBody();
        $body->rewind();
        $data = $body->getContents();

        $compressed = gzencode($data, $this->compressionLevel);

        $newBody = $this->createBody();
        $newBody->write($compressed);

        return $response
                ->withBody($newBody)
                ->withHeader('Content-Encoding', 'gzip');
    }

    /**
     * Check the url path extension for compression support.
     *
     * The extension will be removed from the path if found.
     *
     * @param Request $request
     *
     * @return Request
     */
    protected function checkExtension(Request $request)
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        $ext = $this->extension;

        $offset = max(strlen($path) - strlen($ext), 0);
        if (strpos($path, $ext, $offset) !== false) {
            $this->useCompression = true;

            $path = substr($path, 0, (strlen($ext) * -1));

            return $request->withUri($uri->withPath($path));
        }

        return $request;
    }

    /**
     * Check the Accept-Encoding header for compression support.
     *
     * @param Request $request
     *
     * @return Request
     */
    protected function checkAcceptEncodingHeader(Request $request)
    {
        $acceptHeader = $request->getHeaderLine('Accept-Encoding');
        $acceptHeaders = array_map('trim', explode(',', $acceptHeader));

        if (in_array($this->acceptEncodingHeader, $acceptHeaders)) {
            $this->useCompression = true;
        }

        return $request;
    }

    /**
     * Invoke CompressMiddleware.
     *
     * @param Request  $request
     * @param Response $response
     * @param callable $next
     *
     * @return Response
     */
    public function __invoke(Request $request, Response $response, callable $next)
    {
        if (!$this->useCompression) {
            if ($this->supportExtension) {
                $request = $this->checkExtension($request);
            }

            if ($this->supportAcceptEncodingHeader) {
                $request = $this->checkAcceptEncodingHeader($request);
            }
        }

        /* @var $newResponse Response */
        $newResponse = $next($request, $response);

        if ($this->useCompression) {
            $newResponse = $this->compress($newResponse);
            
        }

        return $newResponse;
    }

    /**
     * Set extension.
     *
     * @param string $extension
     *
     * @return self
     */
    public function setExtension($extension)
    {
        $this->extension = $extension;

        return $this;
    }

    /**
     * Get extension.
     *
     * @return string
     */
    public function getExtension()
    {
        return $this->getExtension;
    }

    /**
     * Set acceptEncodingHeader.
     *
     * @param string $acceptEncodingHeader
     *
     * @return self
     */
    public function setAcceptEncodingHeader($acceptEncodingHeader)
    {
        $this->acceptEncodingHeader = $acceptEncodingHeader;

        return $this;
    }

    /**
     * Get acceptEncodingHeader.
     *
     * @return string
     */
    public function getAcceptEncodingHeader()
    {
        return $this->acceptEncodingHeader;
    }

    /**
     * Set compressionLevel.
     *
     * @param int $compressionLevel
     *
     * @return self
     */
    public function setCompressionLevel($compressionLevel)
    {
        $this->compressionLevel = $compressionLevel;

        return $this;
    }

    /**
     * Get compressionLevel.
     *
     * @return int
     */
    public function getCompressionLevel()
    {
        return $this->compressionLevel;
    }

    /**
     * Set supportExtension.
     *
     * @param bool $supportExtension
     *
     * @return self
     */
    public function setSupportExtension($supportExtension)
    {
        $this->supportExtension = $supportExtension;

        return $this;
    }

    /**
     * Get supportExtension.
     *
     * @return bool
     */
    public function getSupportExtension()
    {
        return $this->supportExtension;
    }

    /**
     * Set supportAcceptEncodingHeader.
     *
     * @param bool $supportAcceptEncodingHeader
     *
     * @return self
     */
    public function setSupportAcceptHeader($supportAcceptEncodingHeader)
    {
        $this->supportAcceptEncodingHeader = $supportAcceptEncodingHeader;

        return $this;
    }

    /**
     * Get supportAcceptEncodingHeader.
     *
     * @return bool
     */
    public function getSupportAcceptHeader()
    {
        return $this->supportAcceptEncodingHeader;
    }

    /**
     * Set useCompression.
     *
     * @param bool $useCompression
     *
     * @return self
     */
    public function setUseCompression($useCompression)
    {
        $this->useCompression = $useCompression;

        return $this;
    }

    /**
     * Get useCompression.
     *
     * @return bool
     */
    public function getUseCompression()
    {
        return $this->useCompression;
    }
}
