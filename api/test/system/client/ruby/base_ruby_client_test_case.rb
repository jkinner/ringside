require "test/unit"
require 'rubygems'
require 'rfacebook'

# Author: wreichardt
class BaseRubyClientTestCase < Test::Unit::TestCase
  
  # Responsible for establishing a session with a facebook server in the name of the
  # registered application with the api key test_case_key-17100 and the secret key of 
  # secretkey. These have been pre-established in the fb repository prior to the running
  # of the unit tests. The fb repository will respond with a authentication token which
  # it can then pair with a particular user's uid to produce an authenticated session.
  #
  # +NOTE+: The auth_approveToken is a non facebook api way of forcing a requested uid+authToken
  # combination to be approved without a user being forced to log in. The result is an authenticated
  # session.
  #
  def setup
    super
    @apiKey = "test_case_key-17100";
    @secretKey = "secretkey";
    @uid="17001";
    @fbsession = RFacebook::FacebookWebSession.new(@apiKey,@secretKey)
    initClient(@uid)
  end

  # A utility method to allow the logged in user to be changed at any time
  def initClient(uid)
    @authToken=@fbsession.auth_createToken(:api_key=>@apiKey)
    @fbsession.auth_approveToken(:auth_token=>@authToken, :uid=>uid)
    @fbsession.activate_with_token(@authToken)    
  end
  
  def teardown
    super
    @fbsession=nil
  end
  
  def default_test
  
  end  
end