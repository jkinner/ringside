require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientEventsTestCase < BaseRubyClientTestCase

  # Tests Events.getMembers
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Events.getMembers
  def testEventsGet
      doc=@fbsession.events_get
      assert_not_nil doc
      event=doc.at("facebook_events_get_response/event")
      assert_not_nil event
      assert_equal "17400",event.at("eid").inner_html
      assert_equal "SOME_EVENT",event.at("name").inner_html
      assert_equal "0",event.at("nid").inner_html
      assert_equal "athome",event.at("host").inner_html
      assert_equal "party",event.at("group_type").inner_html
      assert_equal "hardy",event.at("group_subtype").inner_html
      assert_equal "17001",event.at("creator").inner_html
      assert_equal "on the bank",event.at("location").inner_html
      
      venue=event.at("venue")
      assert_not_nil venue
      assert_equal "mars",venue.at("city").inner_html
      assert_equal "ok",venue.at("state").inner_html
      assert_equal "us",venue.at("country").inner_html
     
  end
  
  # Tests Events.getMembers
  #  Request an event and examine which users have accepted, declined or otherwise
  # +See+:http://wiki.developers.facebook.com/index.php/Events.getMembers
  def testEventsGetMembers
      doc=@fbsession.events_getMembers(:eid=>17400)
      assert_not_nil doc
      attending=doc.at("facebook_events_getMembers_response/attending")
      assert_not_nil attending
      assert_equal "17004",attending.at("uid").inner_html
      unsure=doc.at("facebook_events_getMembers_response/unsure")
      assert_not_nil unsure
      assert_equal "17002",unsure.at("uid").inner_html
      declined=doc.at("facebook_events_getMembers_response/declined")
      assert_not_nil declined
      assert_equal "17003",declined.at("uid").inner_html
      not_replied=doc.at("facebook_events_getMembers_response/not_replied")
      assert_not_nil not_replied
      assert_equal "17001",not_replied.at("uid").inner_html
  end
  
end