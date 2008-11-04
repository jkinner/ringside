require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientUsersTestCase < BaseRubyClientTestCase
   
  # Tests Users.getLoggedInUser 
  # +See+:http://wiki.developers.facebook.com/index.php/Users.getLoggedInUser
  def testUsersGetLoggedInUser
    uid = @fbsession.users_getLoggedInUser;
    assert_equal @uid,uid.to_s
  end
  
  # Tests Users.isAppAdded 
  # +See+:http://wiki.developers.facebook.com/index.php/Users.isAppAdded
  def testUsersIsAppAdded
    assert @fbsession.users_isAppAdded;
  end
  
  # Tests Users.hasAppPermission
  # Request values of all known permissions and verify that they are as expected
  # +See+:http://wiki.developers.facebook.com/index.php/Users.hasAppPermission
  def testUsersHasAppPermission
    doc=@fbsession.users_hasAppPermission(:ext_perm=>"status_update")
    assert_equal("1", doc.at("//facebook_users_hasAppPermission_response").inner_html);                                                            
    doc=@fbsession.users_hasAppPermission(:ext_perm=>"photo_upload")
    assert_equal("0", doc.at("//facebook_users_hasAppPermission_response").inner_html);
    doc=@fbsession.users_hasAppPermission(:ext_perm=>"create_listing")
    assert_equal("1", doc.at("//facebook_users_hasAppPermission_response").inner_html);
  end
  
  # Tests Users.setStatus
  # Request a user's status, change it and then restore it to its original state.
  # +See+:http://wiki.developers.facebook.com/index.php/Users.setStatus
  def testUsersSetStatus
    
    original_message="CooCoo for Coco Puffs"
    
    # Get the status message for user 17001
    doc = @fbsession.users_getInfo(:uids=>["17001"],:fields=>["status"]);
    assert_not_nil doc
    assert_equal(original_message, doc.at("//user/status/message").inner_html)
    
    # Change the status
    doc = @fbsession.users_setStatus(:status=>"writing code",:clear=>"true")
    assert_not_nil doc
    
    # Read Back Change
    doc = @fbsession.users_getInfo(:uids=>["17001"],:fields=>["status"]);
    assert_not_nil doc
    assert_equal("writing code", doc.at("//user/status/message").inner_html)
    
    # Restore original message
    doc = @fbsession.users_setStatus(:status=>original_message,:clear=>"true")
    assert_not_nil doc
    
    # Read Back Change to original message
    doc = @fbsession.users_getInfo(:uids=>["17001"],:fields=>["status"])
    assert_not_nil doc
    assert_equal(original_message, doc.at("//user/status/message").inner_html)
    
  end
  
  # Tests Users.getInfo
  # Request user info and introspect it.
  # +See+:http://wiki.developers.facebook.com/index.php/Users.getInfo
  def testUsersGetInfo
    
    fields = [
      "about_me","activities","affiliations",
      "birthday","books","current_location",
      "education_history","first_name","is_app_user",
      "has_added_app","hometown_location","hs_info",
      "interests","last_name","meeting_for",
      "meeting_sex","movies","music",
      "name","notes_count","pic",
      "pic_big","pic_small","pic_square",
      "political","profile_update_time","quotes",
      "relationship_status","religion","sex",
      "significant_other_id","status","timezone",
      "tv","wall_count","work_history"]
    
    doc = @fbsession.users_getInfo(:uids=>["17001"], :fields=>fields)
    
    # check simple values
    assert_equal("About me - nothing!", doc.at("//about_me").inner_html)
    assert_equal("Boating, pumpkin carving, procuring comestibles, etc. ",doc.at("//activities").inner_html)
    assert_equal("2007-12-31", doc.at("//birthday").inner_html)
    assert_equal("Snakes on a plane, the book", doc.at("//books").inner_html)
    assert_equal("Test1", doc.at("first_name").inner_html)
    assert_equal("Music,sports,tv guide,nuclear physics", doc.at("//interests").inner_html)
    assert_equal("User1", doc.at("//last_name").inner_html)      
    assert_equal("Snakes on a plane", doc.at("//movies").inner_html)      
    assert_equal("Miles Davis,Brittney Spears", doc.at("//music").inner_html)
    assert_equal("Liberal", doc.at("//political").inner_html);
    assert_equal("2008-01-01 00:00:00", doc.at("//profile_update_time").inner_html);    
    assert_equal("Nationalism is an infantile disease - Albert Einstien", doc.at("//quotes").inner_html)
    assert_equal("Single", doc.at("//relationship_status").inner_html)
    assert_equal("Scientologist", doc.at("//religion").inner_html)
    assert_equal("M", doc.at("//sex").inner_html)
    assert_equal("0", doc.at("//significant_other_id").inner_html)      
    assert_equal("-4", doc.at("//timezone").inner_html)
    assert_equal("Telemundo", doc.at("//tv").inner_html)
    
    # check out affiliations
    list_afilliations=doc.search("//affiliation")
    assert_not_nil list_afilliations
    assert_equal 3,list_afilliations.size
    assert_equal "1",list_afilliations[0].at("nid").inner_html
    assert_equal "Philadelphia",list_afilliations[0].at("name").inner_html
    assert_equal "2",list_afilliations[1].at("nid").inner_html
    assert_equal "Arts and Crafts",list_afilliations[1].at("name").inner_html
    assert_equal "3",list_afilliations[2].at("nid").inner_html
    assert_equal "Northeast High",list_afilliations[2].at("name").inner_html
    
    # Confirm current location
    list_locations=doc.search("//current_location")
    assert_not_nil list_locations
    assert_equal 1,list_locations.size
    location=list_locations[0]
    assert_equal "Eightown",location.at("city").inner_html
    assert_equal "NI",location.at("state").inner_html
    assert_equal "USA",location.at("country").inner_html
    assert_equal "87654",location.at("zip").inner_html
    
    # check out education history
    list_education_history=doc.search("//education_history")
    assert_not_nil list_education_history
    assert_equal 1,list_education_history.size
    
    education_history1=list_education_history[0]
    assert_equal "Temple University",education_history1.at("name").inner_html
    assert_equal "1999",education_history1.at("year").inner_html
    
    concentration_list1=education_history1.search("//concentrations")
    assert_equal 2,concentration_list1.size
    assert_equal "Communications",concentration_list1[0].at("concentration").inner_html
    assert_equal "Rocket Science",concentration_list1[1].at("concentration").inner_html
    
    # Test Hometown location
    list_hometown_location=doc.search("//hometown_location")
    assert_not_nil list_hometown_location
    assert_equal 1,list_hometown_location.size
    hometown_location=list_hometown_location[0]
    assert_equal "Eightown",hometown_location.at("city").inner_html
    assert_equal "NI",hometown_location.at("state").inner_html
    assert_equal "USA",hometown_location.at("country").inner_html
    assert_equal "87654",hometown_location.at("zip").inner_html
    
    # Test Meeting For
    list_meeting_for=doc.search("//meeting_for")
    assert_not_nil list_meeting_for
    assert_equal 1,list_meeting_for.size
    list_seeking=list_meeting_for.search("seeking")
    assert_equal "Random Play",list_seeking[0].inner_html
    assert_equal "Whatever I can get",list_seeking[1].inner_html
    
    # Test Meeting Sex
    list_meeting_sex=doc.search("//meeting_sex")
    assert_not_nil list_meeting_sex
    assert_equal 1,list_meeting_sex.size
    meeting_sex_wrapper=list_meeting_sex[0]
    assert_not_nil meeting_sex_wrapper
    list_sex=list_meeting_sex[0].search("sex")
    assert_not_nil list_sex
    assert_equal 2,list_sex.size
    assert_equal "M",list_sex[0].inner_html
    assert_equal "F",list_sex[1].inner_html
    
    # Test Status
    status_message=doc.search("//status/message")
    assert_equal "CooCoo for Coco Puffs",  status_message.inner_html  
    
    # Test Work History
    work_history=doc.search("//work_history")
    assert_equal 1,work_history.size
    
    assert_equal("Spacely Sprockets", work_history.at("company_name").inner_html)         
    assert_equal("Sprocket Engineer", work_history.at("position").inner_html)
    assert_equal("Now is the time on sprockets when we dance", work_history.at("description").inner_html)
    assert_equal("2002-01-01", work_history.at("start_date").inner_html)
    assert_equal("2003-02-04", work_history.at("end_date").inner_html)
    assert_equal("Hairydelphia", work_history.at("location/city").inner_html);
    assert_equal("PA", work_history.at("location/state").inner_html);
    assert_equal("USA", work_history.at("location/country").inner_html);
  end
  
  # Tests Users.getInfo
  # Request friends list
  # +See+:http://wiki.developers.facebook.com/index.php/Users.getInfo
  def testFriendsGet
    doc = @fbsession.friends_get 
    list_friends=doc.search("//uid")
    assert_not_nil list_friends
    assert_equal 3,list_friends.size
    assert_equal "17002",list_friends[0].inner_html
    assert_equal "17003",list_friends[1].inner_html
    assert_equal "17004",list_friends[2].inner_html
  end
end