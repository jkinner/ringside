require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientPagesTestCase < BaseRubyClientTestCase
  
  # Tests Pages.getInfo
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Pages.getInfo
  def testPagesGetInfo
    doc=@fbsession.pages_getInfo(:pids=>[17700],
                                 :fields=>[
      "page_id","founded","mission","website",
      "company_overview", "products","name",
      "type","pic_big","pic_small","pic_square",
      "pic_large","pic"
    ])
    assert_not_nil doc
    #assert_equal 1,doc
    page=doc.at("//page")
    assert_not_nil page
    assert_equal "17700",page.at("page_id").inner_html
    assert_equal "Early last year", page.at("founded").inner_html
    assert_equal "A long mission with a nice vision", page.at("mission").inner_html
    assert_equal "http://mycrazystore.com/", page.at("website").inner_html
    assert_equal "Crazy guys with crazy store", page.at("company_overview").inner_html
    assert_equal "A b C and D ", page.at("products").inner_html      
    assert_equal "CRAZYONLINE", page.at("name").inner_html
    assert_equal "ONLINE_STORE", page.at("type").inner_html
    assert_equal "http://www.picit.com/image1.jpg", page.at("pic_big").inner_html
    assert_equal "http://www.picit.com/image1.jpg", page.at("pic_small").inner_html
    assert_equal "http://www.picit.com/image1.jpg", page.at("pic_square").inner_html
    assert_equal "http://www.picit.com/image1.jpg", page.at("pic_large").inner_html
    assert_equal "http://www.picit.com/image1.jpg", page.at("pic").inner_html
    
  end
  
  # Tests Pages.isFan
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Pages.getInfo
  def testPagesIsFan
    doc=@fbsession.pages_isFan(:page_id=>17700,:uid=>17002)
    assert_equal "1",doc.to_s
    begin
      doc=@fbsession.pages_isFan(:page_id=>17700,:uid=>17005)
      assert_fail
    rescue RFacebook::FacebookSession::RemoteStandardError
      # Its good because it failed because this uid is not a fan
      # of this page
    end
    
  end
  
  # Tests Pages.isAdmin
  # See if the current user is an admin for a page
  # then change the logged in user to a non admin
  # for this page and confirm that his permission 
  # fails
  # +See+:http://wiki.developers.facebook.com/index.php/Pages.isAdmin
  def testPagesIsAdmin
    doc=@fbsession.pages_isAdmin(:page_id=>17700)
    assert_equal "1",doc.to_s
    initClient("17002")
    doc=@fbsession.pages_isAdmin(:page_id=>17700)
    assert_equal "0",doc.to_s
  end
  
  # Tests Pages.isAppAdded
  # 
  # +See+:http://wiki.developers.facebook.com/index.php/Pages.isAppAdded
  def testPagesIsAppAdded
    doc=@fbsession.pages_isAppAdded(:page_id=>17700)  
    assert_equal "1",doc.to_s
  end
  
end