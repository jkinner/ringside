

import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientFeedTestCase extends BaseJavaClientTestCase
{  
   public void testFeedPublishStoryToUser() throws Exception
   {
      //m_client.setDebug(true);
      assertTrue(m_client.feed_publishStoryToUser("newsflash!", "this is a feed."));
      m_client.setDebug(false);
   }
   
   public void testFeedPublishActionOfUser() throws Exception
   {
      assertTrue(m_client.feed_publishActionOfUser("OMG!", "some user did something!"));
   }
   
   
}