require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientProfileTestCase  < BaseRubyClientTestCase
 
 # Tests Profile.getFBML
 # Pull a users profile markup and verify
 # +See+:http://wiki.developers.facebook.com/index.php/Profile.getFBML
 def testProfileGetFBML
      doc = @fbsession.profile_getFBML(:uid=>17001)
      assert_equal "this is some fbml", doc.at("facebook_profile_getFBML_response").inner_html
 end

 # Tests Profile.setFBML
 # Change a users profile markup, verify the change and restore it
 # +See+:http://wiki.developers.facebook.com/index.php/Profile.setFBML
 def testProfileSetFBML
   begin
      doc = @fbsession.profile_setFBML(:markup=>"this is some new fbml",:uid=>17001)
      assert_equal "1",doc.to_s
      doc = @fbsession.profile_getFBML(:uid=>17001)
      assert_equal "this is some new fbml", doc.at("/facebook_profile_getFBML_response").inner_html
    ensure
      doc=@fbsession.profile_setFBML(:markup=>"this is some fbml", :uid=>17001)    
      assert_equal "1",doc.to_s,"Failed to restore database. It is now in an inconsistant state. Please rebuild it."
    end
 end
end