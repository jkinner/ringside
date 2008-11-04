require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientNotificationsTestCase < BaseRubyClientTestCase

  # Tests Notifications.get
  # Look for shares, pokes and messages and examine their status 
  # +See+:http://wiki.developers.facebook.com/index.php/Notifications.get
  def testNotificationsGet
    doc=@fbsession.notifications_get
    assert_not_nil doc
    assert_equal("2", doc.at("//shares/unread").inner_html)
    assert_equal("17202", doc.at("//shares/most_recent").inner_html)
    assert_equal("0", doc.at("//messages/unread").inner_html)
    assert_equal("0", doc.at("//messages/most_recent").inner_html)
    assert_equal("2", doc.at("//pokes/unread").inner_html)
    assert_equal("17003", doc.at("//pokes/most_recent").inner_html)
    assert_equal("17400", doc.at("//event_invites/eid").inner_html)
  end

  # Tests Notifications.send
  # Send a notification and see if the two recepients have a massage waiting for them 
  # +See+:http://wiki.developers.facebook.com/index.php/Notifications.send
  def testNotificationsSend
    doc=@fbsession.notifications_send(:to_ids=>[17002,17003],:notification=>"goats!")
    initClient("17002")
    doc=@fbsession.notifications_get
    assert_equal "1",doc.at("//messages/unread").inner_html
    initClient("17003")
    doc=@fbsession.notifications_get
    assert_equal "1",doc.at("//messages/unread").inner_html
  end
  
  # Tests Notifications.sendEmail
  # Send an email then see of the recipient had it as unread
  # +See+:http://wiki.developers.facebook.com/index.php/Notifications.sendEmail
  def testNotificationsSendEmail
    doc=@fbsession.notifications_sendEmail(:recipients=>[17003],:subject=>"no subject",:text=>"this is the message body")
    initClient(17003)
    doc=@fbsession.notifications_get
    #assert_equal "fro",doc
    assert_equal("1", doc.at("//messages/unread").inner_html)
  end
  
end