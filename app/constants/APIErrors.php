<?php
/**
 * The class contains all api error codes
 */
Class APIErrors
{
    /**
     * Unauthorized
     */
    const ACCESS_DENIED = 101;

    /**
     * Unforeseen exceptions (Catch all exception code, that would trigger sysadmin notifications)
     */
    const SYSTEM_ERROR = 42;

    /**
     * Api timeout error
     */
    const API_TIME_OUT = 408;

    /**
     * Invalid api response from curl
     */
    const INVALID_API_RESPONSE = 409;

    /**
     * Invalid api params supplied
     */
    const INVALID_API_PARAMS = 410;
}