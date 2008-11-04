
import java.util.*;
import java.net.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientPhotosTestCase extends BaseJavaClientTestCase
{  
   public void testPhotosGet() throws Exception
   {
      //m_client.setDebug(true);
      List<Long> pids = new ArrayList<Long>();
      Object resp = m_client.photos_get((long) 17500, pids);
      
      String[] flds = new String[] {"pid", "aid", "owner", "src", "src_big",
                                    "src_small", "link", "caption", "created"};
      
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);
      
      assertEquals("17520", valMap.get("pid"));
      assertEquals("17500", valMap.get("aid"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("http://localhost/img1.jpg", valMap.get("src"));
      assertEquals("http://localhost/img1_big.jpg", valMap.get("src_big"));
      assertEquals("http://localhost/img1_small.jpg", valMap.get("src_small"));
      assertEquals("http://localhost/img1_link.jpg", valMap.get("link"));
      assertEquals("image 1", valMap.get("caption"));
      assertEquals("123", valMap.get("created"));
      
      valMap = extractObjectFields(resp, flds, 1);
      
      assertEquals("17521", valMap.get("pid"));
      assertEquals("17500", valMap.get("aid"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("http://localhost/img2.jpg", valMap.get("src"));
      assertEquals("http://localhost/img2_big.jpg", valMap.get("src_big"));
      assertEquals("http://localhost/img2_small.jpg", valMap.get("src_small"));
      assertEquals("http://localhost/img2_link.jpg", valMap.get("link"));
      assertEquals("image 2", valMap.get("caption"));
      assertEquals("1234", valMap.get("created"));
      
      resp = m_client.photos_get((long) 17501, pids);
      
      valMap = extractObjectFields(resp, flds, 0);
      
      assertEquals("17522", valMap.get("pid"));
      assertEquals("17501", valMap.get("aid"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("http://localhost/img3.jpg", valMap.get("src"));
      assertEquals("http://localhost/img3_big.jpg", valMap.get("src_big"));
      assertEquals("http://localhost/img3_small.jpg", valMap.get("src_small"));
      assertEquals("http://localhost/img3_link.jpg", valMap.get("link"));
      assertEquals("image 3", valMap.get("caption"));
      assertEquals("12345", valMap.get("created"));
      
      valMap = extractObjectFields(resp, flds, 1);
      
      assertEquals("17523", valMap.get("pid"));
      assertEquals("17501", valMap.get("aid"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("http://localhost/img4.jpg", valMap.get("src"));
      assertEquals("http://localhost/img4_big.jpg", valMap.get("src_big"));
      assertEquals("http://localhost/img4_small.jpg", valMap.get("src_small"));
      assertEquals("http://localhost/img4_link.jpg", valMap.get("link"));
      assertEquals("image 4", valMap.get("caption"));
      assertEquals("123456", valMap.get("created"));

      valMap = extractObjectFields(resp, flds, 2);
      
      assertEquals("17524", valMap.get("pid"));
      assertEquals("17501", valMap.get("aid"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("http://localhost/img5.jpg", valMap.get("src"));
      assertEquals("http://localhost/img5_big.jpg", valMap.get("src_big"));
      assertEquals("http://localhost/img5_small.jpg", valMap.get("src_small"));
      assertEquals("http://localhost/img5_link.jpg", valMap.get("link"));
      assertEquals("image 5", valMap.get("caption"));
      assertEquals("1234567", valMap.get("created"));
      
      m_client.setDebug(false);
   }
   
   public void testPhotosGetAlbums() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.photos_getAlbums(17001);
      
      String[] flds = new String[] {"aid","name","owner","created","description",
                                    "location","link"};
      
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);

      assertEquals("17500", valMap.get("aid"));
      assertEquals("test album 1", valMap.get("name"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("123456", valMap.get("created"));
      assertEquals("test album 1 description", valMap.get("description"));
      assertEquals("antartica", valMap.get("location"));
      assertEquals("http://www.ringside.com/album.php?aid=17500&id=17001", URLDecoder.decode(valMap.get("link"), "UTF-8"));
      
      valMap = extractObjectFields(resp, flds, 1);

      assertEquals("17501", valMap.get("aid"));
      assertEquals("test album 2", valMap.get("name"));
      assertEquals("17001", valMap.get("owner"));
      assertEquals("1234567", valMap.get("created"));
      assertEquals("test album 2 description", valMap.get("description"));
      assertEquals("new z-land", valMap.get("location"));
      assertEquals("http://www.ringside.com/album.php?aid=17501&id=17001", URLDecoder.decode(valMap.get("link"), "UTF-8"));
      
      m_client.setDebug(false);
   }
   
   public void testPhotosCreateAlbum() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.photos_createAlbum("hot new album", "bring a change of underwear", "north pole");
         
      Long aid = null;
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         Node a = xresp.getDocumentElement();
         Node n = getChildNodeByName(a, "aid");
         assertNotNull(n);
         aid = Long.parseLong(n.getTextContent());
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONObject obj = (JSONObject) resp;
         aid = Long.parseLong(obj.get("aid").toString());
      }
      assertNotNull(aid);
      
      List<Long> aids = new ArrayList<Long>();     
      aids.add(aid);
      resp = m_client.photos_getAlbums(17001, aids);
      
      String[] flds = new String[] {"aid","name","owner","description","location"};
      
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);

      assertEquals(aid.toString(), valMap.get("aid"));
      assertEquals("hot new album", valMap.get("name"));
      assertEquals("17001", valMap.get("owner"));      
      assertEquals("bring a change of underwear", valMap.get("description"));
      assertEquals("north pole", valMap.get("location"));
      
      m_client.setDebug(false);
   }
   
   public void testPhotosGetTags() throws Exception
   {
      //m_client.setDebug(true);
      List<Long> pids = new ArrayList<Long>();
      pids.add((long) 17520);
      Object resp = m_client.photos_getTags(pids);
      
      String[] flds = new String[] {"pid", "subject", "xcoord", "ycoord", "created"};
      
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);
      
      assertEquals("17520", valMap.get("pid"));
      assertEquals("17001", valMap.get("subject"));
      assertEquals("0", valMap.get("xcoord"));
      assertEquals("0", valMap.get("ycoord"));
      assertEquals("123", valMap.get("created"));
      
      valMap = extractObjectFields(resp, flds, 1);
      
      assertEquals("17520", valMap.get("pid"));
      assertEquals("17001", valMap.get("subject"));
      assertEquals("100", valMap.get("xcoord"));
      assertEquals("100", valMap.get("ycoord"));
      assertEquals("1234", valMap.get("created"));
      
      m_client.setDebug(false);
   }
   
   public void testPhotosAddTag() throws Exception
   {
      //m_client.setDebug(true);
      assertTrue(m_client.photos_addTag((long) 17521, 17001, 57.0, 57.0));
      
      List<Long> pids = new ArrayList<Long>();
      pids.add((long) 17521);
      Object resp = m_client.photos_getTags(pids);
      
      String[] flds = new String[] {"pid", "subject", "xcoord", "ycoord"};
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);
      
      assertEquals("17521", valMap.get("pid"));
      assertEquals("17001", valMap.get("subject"));
      assertEquals("57", valMap.get("xcoord"));
      assertEquals("57", valMap.get("ycoord"));
      
      m_client.setDebug(false);
   }
}