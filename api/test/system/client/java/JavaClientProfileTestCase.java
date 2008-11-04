import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientProfileTestCase extends BaseJavaClientTestCase
{
   public void testProfileGetFBML() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.profile_getFBML(17001);      
      
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         assertEquals("this is some fbml", xresp.getDocumentElement().getTextContent());
         
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         assertEquals("this is some fbml", (String) resp);
      }      
      m_client.setDebug(false);
   }
   
   public void testProfileSetFBML() throws Exception
   {
      assertTrue(m_client.profile_setFBML("this is some new fbml", 17001));      
      Object resp = m_client.profile_getFBML(17001);
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         assertEquals("this is some new fbml", xresp.getDocumentElement().getTextContent());
         
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         assertEquals("this is some new fbml", (String) resp);
      }     
      
      assertTrue(m_client.profile_setFBML("this is some fbml", 17001));
   }
   
   
   
}