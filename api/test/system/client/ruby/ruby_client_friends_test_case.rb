require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientFriendsTestCase < BaseRubyClientTestCase
  
  # Tests Friends.areFriends
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Friends.areFriends
  def testFriendsAreFriends
    # Are 17001 and 17002 friends?
    doc=@fbsession.friends_areFriends(:uids1=>[17001],:uids2=>[17002])
    assert_not_nil doc
    assert_equal "17001",doc.at("friend_info/uid1").inner_html
    assert_equal "17002",doc.at("friend_info/uid2").inner_html
    assert_equal "1",doc.at("friend_info/are_friends").inner_html

    # Are 17001 and 17003 friends?
    doc=@fbsession.friends_areFriends(:uids1=>[17001],:uids2=>[17003])
    assert_not_nil doc
    assert_equal "17001",doc.at("friend_info/uid1").inner_html
    assert_equal "17003",doc.at("friend_info/uid2").inner_html
    assert_equal "1",doc.at("friend_info/are_friends").inner_html

    # Are 17001 and 17004 friends?
    doc=@fbsession.friends_areFriends(:uids1=>[17001],:uids2=>[17004])
    assert_not_nil doc
    assert_equal "17001",doc.at("friend_info/uid1").inner_html
    assert_equal "17004",doc.at("friend_info/uid2").inner_html
    assert_equal "1",doc.at("friend_info/are_friends").inner_html

    # Are 17001 and 17005 friends?
    doc=@fbsession.friends_areFriends(:uids1=>[17001],:uids2=>[17005])
    assert_not_nil doc
    assert_equal "17001",doc.at("friend_info/uid1").inner_html
    assert_equal "17005",doc.at("friend_info/uid2").inner_html
    assert_equal "0",doc.at("friend_info/are_friends").inner_html

    # Are 17001 and 17999 friends?
    doc=@fbsession.friends_areFriends(:uids1=>[17001],:uids2=>[17999])
    assert_not_nil doc
    assert_equal "17001",doc.at("friend_info/uid1").inner_html
    assert_equal "17999",doc.at("friend_info/uid2").inner_html
    assert_equal "0",doc.at("friend_info/are_friends").inner_html
  end
  
  # Tests Friends.areAppUsers
  # While friends of yours also use this app?
  # +See+:http://wiki.developers.facebook.com/index.php/Friends.Friends.areAppUsers
  def testFriendsAreAppUsers
    doc=@fbsession.friends_getAppUsers
    assert_not_nil doc
    uids=doc.search("//uid")
    assert_equal 2,uids.size
    assert_equal "17002",uids[0].inner_html
    assert_equal "17003",uids[1].inner_html
  end
  
end