
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientFbmlTestCase extends BaseJavaClientTestCase
{  
   public void testFbmlSetRefHandle() throws Exception
   {
      //m_client.setDebug(true);
      //TODO: check results
      assertTrue(m_client.fbml_setRefHandle("testHandle", "http://www.osadvisors.com/docs/test.txt"));
      m_client.setDebug(false);
   }
   
   public void testFbmlRefreshRefUrl() throws Exception
   {
      //m_client.setDebug(true);
      //TODO: check results
      assertTrue(m_client.fbml_refreshRefUrl("http://www.osadvisors.com/docs/test.html"));
      m_client.setDebug(false);
   }
   
   public void testFbmlRefreshImgSrc() throws Exception
   {
      //m_client.setDebug(true);
      //TODO: check results
      assertTrue(m_client.fbml_refreshImgSrc("http://www.osadvisors.com/images/test.gif"));
      m_client.setDebug(false);
   }
   
   
   
   
   
}