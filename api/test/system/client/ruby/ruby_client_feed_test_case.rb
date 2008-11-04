require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientFeedTestCase < BaseRubyClientTestCase
  # Tests Feed.publishStoryToUser
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Feed.publishStoryToUser
  def testFeedPublishStoryToUser
    doc=@fbsession.feed_publishStoryToUser(:title=>"newsflash!", :body=>"this is a feed.")
    assert_equal "1",doc.at("facebook_feed_publishStoryToUser_response").inner_html
  end
  
  # Tests Feed.publishActionOfUser
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Feed.publishActionOfUser
  def testFeedPublishActionOfUser
    doc=@fbsession.feed_publishActionOfUser(:title=>"OMG!", :body=>"some user did something!")
    assert_equal "1",doc.at("facebook_feed_publishActionOfUser_response").inner_html
  end
end