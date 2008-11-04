require File.dirname(__FILE__) + '/base_ruby_client_test_case'

# Author: wreichardt
class RubyClientPhotosTestCase < BaseRubyClientTestCase
  # Tests Photos.get
  # Ask for all the photos from album id 17500, verify their metadata
  # +See+:http://wiki.developers.facebook.com/index.php/Photos.get
  def testPhotosGet
    doc = @fbsession.photos_get(:aid=>17500, :pids=>[])
    assert_not_nil doc
    photos=doc.search("//photo")
    assert_not_nil photos
    assert_equal 2,photos.size

    # Verify the first photo
    photo1=photos[0]
    assert_equal "17520",photo1.at("pid").inner_html
    assert_equal "17500",photo1.at("aid").inner_html
    assert_equal "17001", photo1.at("owner").inner_html
    assert_equal "http://localhost/img1.jpg",photo1.at("src").inner_html
    assert_equal "http://localhost/img1_big.jpg", photo1.at("src_big").inner_html
    assert_equal "http://localhost/img1_small.jpg",photo1.at("src_small").inner_html
    assert_equal "http://localhost/img1_link.jpg",photo1.at("link").inner_html
    assert_equal "image 1", photo1.at("caption").inner_html
    assert_equal "123", photo1.at("created").inner_html

    # Verify the second photo
    photo2=photos[1]
    assert_equal "17521", photo2.at("pid").inner_html
    assert_equal "17500", photo2.at("aid").inner_html
    assert_equal "17001", photo2.at("owner").inner_html
    assert_equal "http://localhost/img2.jpg", photo2.at("src").inner_html
    assert_equal "http://localhost/img2_big.jpg", photo2.at("src_big").inner_html
    assert_equal "http://localhost/img2_small.jpg", photo2.at("src_small").inner_html
    assert_equal "http://localhost/img2_link.jpg", photo2.at("link").inner_html
    assert_equal "image 2", photo2.at("caption").inner_html
    assert_equal "1234", photo2.at("created").inner_html
    
  end
  
  # Tests Photos.getAlbums
  # Request two pre existing albums of a user and then verify them
  # +See+:http://wiki.developers.facebook.com/index.php/Photos.getAlbums
  def testPhotosGetAlbums
    
    # get all albums of user 17001
    doc=@fbsession.photos_getAlbums(:uid=>17001,:aids=>["17500","17501"])
    assert_not_nil doc
    albums=doc.search("//album")
    assert_not_nil albums
    assert_equal 2,albums.size
    
    # Verify album 1
    album1=albums[0]
    assert_equal"17500", album1.at("aid").inner_html
    assert_equal"test album 1", album1.at("name").inner_html
    assert_equal"17001", album1.at("owner").inner_html
    assert_equal"123456", album1.at("created").inner_html
    assert_equal"test album 1 description", album1.at("description").inner_html
    assert_equal"antartica", album1.at("location").inner_html
    assert_equal"http://www.ringside.com/album.php?aid=17500&amp;id=17001", album1.at("link").inner_html

    # Verify album 2
    album2=albums[1]
    assert_equal "17501", album2.at("aid").inner_html
    assert_equal "test album 2", album2.at("name").inner_html
    assert_equal "17001", album2.at("owner").inner_html
    assert_equal "1234567", album2.at("created").inner_html
    assert_equal "test album 2 description", album2.at("description").inner_html
    assert_equal "new z-land", album2.at("location").inner_html
    assert_equal "http://www.ringside.com/album.php?aid=17501&amp;id=17001", album2.at("link").inner_html

  end
  
  # Tests Photos.createAlbum
  # This operation leaves new albums under this users account when done.
  # +See+:http://wiki.developers.facebook.com/index.php/Photos.createAlbum
  def testPhotosCreateAlbum
    
    # create a new album
    doc=@fbsession.photos_createAlbum(:name=>"hot new album", 
              :location=>"north pole",
              :description=>"bring a change of underwear")
    newAid=doc.at("//aid").inner_html
    assert_not_nil newAid
    
    # use the new album id to request the album
    doc=@fbsession.photos_getAlbums(:uid=>17001,:aids=>[newAid])
    albums=doc.search("//album")
    assert_not_nil albums
    assert_equal 1,albums.size

    # Verify album
    album1=albums[0]
    
    assert_equal newAid, album1.at("aid").inner_html
    assert_equal "hot new album", album1.at("name").inner_html
    assert_equal "17001", album1.at("owner").inner_html      
    assert_equal "bring a change of underwear", album1.at("description").inner_html
    assert_equal "north pole", album1.at("location").inner_html


  end
  
  # Tests Photos.getTags
  # Request tags of a specidic photo and verify them
  # +See+:http://wiki.developers.facebook.com/index.php/Photos.getTags 
  def testPhotosGetTags
    doc=@fbsession.photos_getTags(:pids=>["17520"])
    assert_not_nil doc
    assert_equal "17520",doc.search("//pid")[0].inner_html
    assert_equal "17001",doc.search("//subject")[0].inner_html
    assert_equal "0",doc.search("//xcoord")[0].inner_html
    assert_equal "0",doc.search("//ycoord")[0].inner_html
    assert_equal "123",doc.search("//created")[0].inner_html

    assert_equal "17520",doc.search("//pid")[1].inner_html
    assert_equal "17001",doc.search("//subject")[1].inner_html
    assert_equal "100",doc.search("//xcoord")[1].inner_html
    assert_equal "100",doc.search("//ycoord")[1].inner_html
    assert_equal "1234",doc.search("//created")[1].inner_html

  end
  
  # Tests Photos.addTag
  # Adds a tag to an existing photo and verifys it
  # +See+:http://wiki.developers.facebook.com/index.php/Photos.addTag
  def testPhotosAddTag
    doc=@fbsession.photos_addTag(:pid=>17521,:tag_uid=>17001,:x=>57.0,:y=>57.0)
    assert_equal "1",doc.to_s
    doc=@fbsession.photos_getTags(:pids=>[17521])
    assert_not_nil doc
    assert_equal "17521", doc.at("//pid").inner_html
    assert_equal "17001", doc.at("//subject").inner_html
    assert_equal "57", doc.at("//xcoord").inner_html
    assert_equal "57", doc.at("//ycoord").inner_html
  end
  
end