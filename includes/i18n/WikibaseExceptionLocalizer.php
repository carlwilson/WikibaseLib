<?php

namespace Wikibase\i18n;

use Exception;
use Message;
use MessageException;
use ValueParsers\ParseException;
use Wikibase\ChangeOp\ChangeOpValidationException;
use Wikibase\Validators\ValidatorErrorLocalizer;

/**
 * ExceptionLocalizer implementing localization of some well known types of exceptions
 * that may occur in the context of the Wikibase exception.
 *
 * This hardcodes knowledge about different kinds of exceptions. A more generic approach
 * based on a dispatcher can be implemented later if needed.
 *
 * @todo: Extend the interface to allow multiple messages to be returned, for use
 *        with chained exceptions, multiple validation errors, etc.
 *
 * @license GPL 2+
 * @author Daniel Kinzler
 */
class WikibaseExceptionLocalizer implements ExceptionLocalizer {

	/**
	 * @var ValidatorErrorLocalizer
	 */
	protected $validatorErrorLocalizer;

	public function __construct() {
		$this->validatorErrorLocalizer = new ValidatorErrorLocalizer();
	}

	/**
	 * @see ExceptionLocalizer::getExceptionMessage()
	 *
	 * @param Exception $ex
	 *
	 * @return Message
	 */
	public function getExceptionMessage( Exception $ex ) {
		if ( $ex instanceof MessageException ) {
			return $this->getMessageExceptionMessage( $ex );
		} elseif ( $ex instanceof ParseException ) {
			return $this->getParseExceptionMessage( $ex );
		} elseif ( $ex instanceof ChangeOpValidationException ) {
			return $this->getChangeOpValidationExceptionMessage( $ex );
		} else {
			return $this->getGenericExceptionMessage( $ex );
		}
	}

	/**
	 * @param MessageException $messageException
	 *
	 * @return Message
	 */
	protected function getMessageExceptionMessage( MessageException $messageException ) {
		$key = $messageException->getKey();
		$params = $messageException->getParams();
		$msg = wfMessage( $key )->params( $params );

		return $msg;
	}

	/**
	 * @param ParseException $parseError
	 *
	 * @return Message
	 */
	protected function getParseExceptionMessage( ParseException $parseError ) {
		$key = 'wikibase-parse-error';
		$params = array();
		$msg = wfMessage( $key )->params( $params );

		return $msg;
	}

	/**
	 * @param ChangeOpValidationException $ex
	 *
	 * @return Message
	 */
	public function getChangeOpValidationExceptionMessage( ChangeOpValidationException $ex ) {
		$result = $ex->getValidationResult();

		foreach ( $result->getErrors() as $error ) {
			$msg = $this->validatorErrorLocalizer->getErrorMessage( $error );
			return $msg;
		}

		return wfMessage( 'wikibase-validator-invalid' );
	}

	/**
	 * @param Exception $error
	 *
	 * @return Message
	 */
	protected function getGenericExceptionMessage( Exception $error ) {
		$key = 'wikibase-error-unexpected';
		$params = array( $error->getMessage() );
		$msg = wfMessage( $key )->params( $params );

		return $msg;
	}

	/**
	 * @see ExceptionLocalizer::getExceptionMessage()
	 *
	 * @param Exception $ex
	 *
	 * @return bool Always true, since WikibaseExceptionLocalizer is able to provide
	 *         a Message for any kind of exception.
	 */
	public function hasExceptionMessage( Exception $ex ) {
		return true;
	}
}
