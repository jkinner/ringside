require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientFbmlTestCase < BaseRubyClientTestCase
  # Tests Fbml.setHandle
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Fbml.setRefHandle
  def testFbmlSetRefHandle
    doc=@fbsession.fbml_setRefHandle(:handle=>"testHandle",:fbml=>"http://www.testhandle.com")
    assert_equal "1",doc.at("facebook_fbml_setRefHandle_response").inner_html
  end
  
  # Tests Fbml.refreshRefUrl
  #
  # +See+:http://wiki.developers.facebook.com/index.php/Fbml.refreshRefUrl
  def testFbmlRefreshRefUrl
    doc=@fbsession.fbml_refreshRefUrl(:url=>"http://www.testhandle.com")
    assert_equal "1",doc.at("facebook_fbml_refreshRefUrl_response").inner_html    
  end
  
  # Tests Fbml.refreshImgSrc
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Fbml.refreshImgSrc
  def testFbmlRefreshImgSrc
    doc=@fbsession.fbml_refreshImgSrc(:url=>"http://www.testhandle.com/someimage.jpg")
    assert_equal "1",doc.at("facebook_fbml_refreshImgSrc_response").inner_html
    
  end
end