<?php

namespace G4\CleanCore\Service;

use G4\CleanCore\Response\Response;
use G4\CleanCore\Request\Request;

abstract class ServiceAbstract implements \G4\CleanCore\Service\ServiceInterface
{
    /**
     * @var \G4\CleanCore\Request\Request
     */
    private $request;

    /**
     * @var \G4\CleanCore\Response\Response
     */
    private $response;

    /**
     * @var \G4\CleanCore\UseCase\UseCaseAbstract
     */
    private $_useCase;

    /**
     * @var \G4\CleanCore\Validator\Validator
     */
    private $_validator;

    public function areParamsValid()
    {
        return $this->getValidator()
            ->setRequest($this->request)
            ->setMeta($this->getMeta())
            ->setWhitelistParams($this->getWhitelistParams())
            ->isValid();
    }

    public function getFormattedResponse()
    {
        if (!method_exists($this->_useCase, 'getFormatterInstance')) {
            $this->response->setResponseObject($this->_getFormattedResource());
        }
        return $this->response;
    }

    public function getValidator()
    {
        if (!$this->_validator instanceof \G4\CleanCore\Validator\Validator) {
            $this->_validator = $this->getValidatorInstance();
        }
        return $this->_validator;
    }

    public function getValidatorInstance()
    {
        return new \G4\CleanCore\Validator\Validator();
    }

    public function getWhitelistParams()
    {
        return array();
    }

    public function run()
    {
        $this->areParamsValid()
            ? $this->runUseCase()
            : $this->response
                ->setHttpResponseCode(400)
                ->setResponseMessage($this->getValidator()->getErrorMessages());

         return $this;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function runUseCase()
    {
        $this->_useCase = $this->getUseCaseInstance();
        $this->_useCase
            ->setRequest($this->request)
            ->setResponse($this->response)
            ->run();

        $this->response = $this->_useCase->getResponse();

        return $this;
    }

    /**
     * @param \G4\CleanCore\Request\Request $request
     * @return \G4\CleanCore\Service\ServiceAbstract
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @param \G4\CleanCore\Response\Response $response
     * @return \G4\CleanCore\Service\ServiceAbstract
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    private function _getFormattedResource()
    {
        return $this->response->hasResponseObject()
            ? $this->_formatterFactory()
            : null;
    }

    private function _formatterFactory()
    {
        return $this->getFormatterInstance()
            ->setResource($this->response->getResponseObject())
            ->format();
    }
}