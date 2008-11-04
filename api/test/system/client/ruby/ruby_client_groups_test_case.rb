require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientGroupsTestCase < BaseRubyClientTestCase

  # Tests Groups.get
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Groups.get
  def testGroupsGet
      doc=@fbsession.groups_get
      assert_not_nil doc
      groups=doc.search("//group")
      assert_not_nil groups
      assert_equal 2,groups.size
      
      # Test the metadata about the first group
      group1=groups[0]
      assert_equal "17800",group1.at("gid").inner_html
      assert_equal "group 1", group1.at("name").inner_html
      assert_equal "0", group1.at("nid").inner_html
      assert_equal "this is group 1",group1.at("description").inner_html
      assert_equal "Awesome Group", group1.at("group_type").inner_html
      assert_equal "Awesome sub-group", group1.at("group_subtype").inner_html
      assert_equal "No news is good news",group1.at("recent_news").inner_html
      assert_equal "17001", group1.at("creator").inner_html
      assert_equal "Suite 55", group1.at("office").inner_html
      assert_equal "http://www.nowhere.com", group1.at("website").inner_html
      assert_equal "nobody@nowhere.com", group1.at("email").inner_html
      assert_equal "123 4th St.", group1.at("venue/street").inner_html
      assert_equal "Nowherapolis", group1.at("venue/city").inner_html
      assert_equal "ZZ", group1.at("venue/state").inner_html
      assert_equal "France", group1.at("venue/country").inner_html

      # Do a quick check of the content in the second group
      group2=groups[1]
      assert_equal "17801",group2.at("gid").inner_html
      assert_equal "group 2", group2.at("name").inner_html
      assert_equal "0", group2.at("nid").inner_html
      assert_equal "17002", group2.at("creator").inner_html
      
  end
  
  # Tests Groups.getMembers
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Groups.getMembers
  def testGroupsGetMembers
    doc = @fbsession.groups_getMembers(:gid=>17800)
    assert_not_nil doc
      
    # Check regular members
    members=doc.search("//members")
    assert_not_nil members
    assert_equal 1,members.size
    uids=members.search("uid")
    assert_equal 1,containsUid(17001,uids)
    assert_equal 1,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
    
    # Which uids are admins 
    admins=doc.search("//admins")
    assert_not_nil admins
    uids=admins.search("uid")
    assert_equal 1,containsUid(17001,uids)
    assert_equal 0,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
    
    # Which uids are officers 
    officers=doc.search("//officers")
    assert_not_nil officers
    uids=officers.search("uid")
    assert_equal 0,containsUid(17001,uids)
    assert_equal 1,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 1,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
 
    # Which uids are not_replied 
    not_replied=doc.search("//not_replied")
    assert_not_nil not_replied
    uids=not_replied.search("uid")
    assert_equal 0,containsUid(17001,uids)
    assert_equal 0,containsUid(17002,uids)
    assert_equal 1,containsUid(17003,uids)
    assert_equal 1,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)

    # Switch to group 17801
    doc = @fbsession.groups_getMembers(:gid=>17801)
    assert_not_nil doc
 
     # Check regular members
    members=doc.search("//members")
    assert_not_nil members
    assert_equal 1,members.size
    uids=members.search("uid")
    assert_equal 1,containsUid(17001,uids)
    assert_equal 1,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
    
    # Which uids are admins 
    admins=doc.search("//admins")
    assert_not_nil admins
    uids=admins.search("uid")
    assert_equal 0,containsUid(17001,uids)
    assert_equal 1,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
    
    # Which uids are officers 
    officers=doc.search("//officers")
    assert_not_nil officers
    uids=officers.search("uid")
    assert_equal 1,containsUid(17001,uids)
    assert_equal 0,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)
 
    # Which uids are not_replied 
    not_replied=doc.search("//not_replied")
    assert_not_nil not_replied
    uids=not_replied.search("uid")
    assert_equal 0,containsUid(17001,uids)
    assert_equal 0,containsUid(17002,uids)
    assert_equal 0,containsUid(17003,uids)
    assert_equal 0,containsUid(17004,uids)
    assert_equal 0,containsUid(17005,uids)

  end
  
  def containsUid(testUid,uids)
    contains=0
    uids.each { |uid| 
    if uid.inner_html==testUid.to_s         
      contains=1 
    end  
    }  
    return contains
  end
end