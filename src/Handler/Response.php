<?php

namespace Hafael\HttpClient\Handler;

use Hafael\HttpClient\Exceptions\ClientException;
use Hafael\HttpClient\Exceptions\ServerException;
use Hafael\HttpClient\Exceptions\ValidationException;

class Response
{
    /**
     * @var int
     */
    protected $statusCode;

    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var string
     */
    protected $content;

    /**
     * @var string
     */
    protected $responseLog;

    /**
     * @var array
     */
    protected $decoded;

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param int $statusCode
     * @return Response
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * @param string $contentType
     * @return Response
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
        return $this;
    }

    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    
    /**
     * @param string $content
     * @return Response
     */
    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    /**
     * @param string $content
     * @return Response
     */
    public function setResponseLog($content)
    {
        $this->responseLog = $content;
        return $this;
    }

    /**
     * @return string
     */
    public function log()
    {
        return $this->responseLog;
    }

    /**
     * @param  string|null  $key
     * @param  mixed  $default
     * @return mixed
     */
    public function json($key = null, $default = null)
    {

        if (! $this->decoded) {
            $this->decoded = json_decode($this->getContent(), true);
        }

        if (is_null($key)) {
            return $this->decoded;
        }

        return data_get($this->decoded, $key, $default);
    }

    /**
     * Get the body of the response.
     *
     * @return string
     */
    public function body()
    {
        return (string) $this->getContent();
    }

    /**
     * Get the JSON decoded body of the response as an object.
     *
     * @return object
     */
    public function object()
    {
        return json_decode($this->getContent(), false);
    }

    /**
     * @return object
     */
    public function toArray()
    {
        return json_decode($this->content, true);
    }

    /**
     * @return bool
     */
    public function successful()
    {
        return $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * @return bool
     */
    public function ok()
    {
        return $this->getStatusCode() === 200 && $this->respondSuccess();
    }

    /**
     * @return bool
     */
    public function redirect()
    {
        return $this->statusCode >= 300 && $this->statusCode < 400;
    }

    /**
     * @return bool
     */
    public function failed()
    {
        return $this->serverError() || $this->clientError();
    }

    /**
     * @return bool
     */
    public function unauthorized()
    {
        return $this->statusCode === 401;
    }

    /**
     * @return bool
     */
    public function forbidden()
    {
        return $this->statusCode === 403;
    }

    /**
     * @return bool
     */
    public function clientError()
    {
        return $this->statusCode >= 400 && $this->statusCode < 500;
    }

    /**
     * @return bool
     */
    public function serverError()
    {
        return $this->statusCode >= 500;
    }

    /**
     * @return bool
     */
    public function respondSuccess()
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300; 
    }

    /**
     * @return string
     */
    public function errorMessage()
    {
        if($this->respondError()) {
            return $this->json();
        }
        return null;
    }

    /**
     * @return bool
     */
    public function respondError()
    {
        return ! $this->respondSuccess();
    }

    /**
     * @return array
     */
    public function validationErrors()
    {
        if($this->respondError() && isset($this->json()['errors']) && !empty(isset($this->json()['errors']))) {
            return array_map(function($error) {
                return $error;
            }, $this->json()['errors']);
        }
        return [];
    }

    /**
     * @return bool
     */
    public function isValidationError()
    {
        return $this->respondError() && !empty($this->validationErrors());
    }

    /**
     * @return bool
     */
    public function throw()
    {
        if(!$this->ok() && $this->isValidationError()) {
            throw (new ValidationException($this->errorMessage(), 422))->setValidationErrors($this->validationErrors());
        }else if((!$this->ok() && $this->respondError())) {
            throw new ClientException($this->errorMessage(), 400);
        }
        throw new ServerException('Server error', 500);

    }
    
}