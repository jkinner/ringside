package org.ringside.client.api.m3;

import org.ringside.client.api.m3.schema.ErrorResponse;

/**
 * This is thrown when the M3 JAXB client receives an error response from a request.
 * 
 * @author John Mazzitelli
 */
public class ErrorResponseException extends Exception
{
	private static final long serialVersionUID = 1L;
	
	private ErrorResponse errorResponse;

	public ErrorResponseException(String msg, ErrorResponse response)
	{
		super(msg);
		this.errorResponse = response;
	}

	public ErrorResponseException(String msg, ErrorResponse response, Exception e)
	{
		super(msg, e);
		this.errorResponse = response;
	}

	/**
	 * Returns the error response that was received from the server.
	 * 
	 * @return error response
	 */
	public ErrorResponse getErrorResponse()
	{
		return this.errorResponse;
	}

	@Override
	public String getLocalizedMessage()
	{
		StringBuilder msg = new StringBuilder(super.getLocalizedMessage());
		msg.append(" - error code=[");
		msg.append(getErrorResponse().getErrorCode());
		msg.append("]; error msg=[");
		msg.append(getErrorResponse().getErrorMsg());
		msg.append("]");
		return msg.toString();
	}
}
