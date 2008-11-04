
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientEventsTestCase extends BaseJavaClientTestCase
{  
   public void testEventsGet() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.events_get(null, null, null, null);
      
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         Node e = clist.item(0);
         
         Node n = getChildNodeByName(e, "eid");
         assertNotNull(n);
         assertEquals("17400", n.getTextContent());         
         n = getChildNodeByName(e, "name");
         assertNotNull(n);
         assertEquals("SOME_EVENT", n.getTextContent());
         n = getChildNodeByName(e, "nid");
         assertNotNull(n);
         assertEquals("0", n.getTextContent());
         n = getChildNodeByName(e, "host");
         assertNotNull(n);
         assertEquals("athome", n.getTextContent());
         n = getChildNodeByName(e, "group_type");
         assertNotNull(n);
         assertEquals("party", n.getTextContent());
         n = getChildNodeByName(e, "group_subtype");
         assertNotNull(n);
         assertEquals("hardy", n.getTextContent());
         n = getChildNodeByName(e, "creator");
         assertNotNull(n);
         assertEquals("17001", n.getTextContent());
         n = getChildNodeByName(e, "location");
         assertNotNull(n);
         assertEquals("on the bank", n.getTextContent());
         
         Node v = getChildNodeByName(e, "venue");
         assertNotNull(v);
         n = getChildNodeByName(v, "city");
         assertNotNull(n);
         assertEquals("mars", n.getTextContent());
         n = getChildNodeByName(v, "state");
         assertNotNull(n);
         assertEquals("ok", n.getTextContent());
         n = getChildNodeByName(v, "country");
         assertNotNull(n);
         assertEquals("us", n.getTextContent());
         
         
      } else if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONArray arr = (JSONArray) resp;
         JSONObject e = (JSONObject) arr.get(0);
         
         assertEquals("17400", e.get("eid"));
         assertEquals("SOME_EVENT", e.get("name"));
         assertEquals("0", e.get("nid"));
         assertEquals("athome", e.get("host"));
         assertEquals("party", e.get("group_type"));
         assertEquals("hardy", e.get("group_subtype"));
         assertEquals("17001", e.get("creator"));
         assertEquals("on the bank", e.get("location"));
         
         JSONObject v = (JSONObject) e.get("venue");
         assertEquals("mars", v.get("city"));
         assertEquals("ok", v.get("state"));
         assertEquals("us", v.get("country"));
      }
      m_client.setDebug(false);
   }
   
   public void testEventsGetMembers() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.events_getMembers(17400);
      
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         
         Node root = xresp.getDocumentElement();        
         NodeList clist = root.getChildNodes();
         
         Node n = clist.item(0);
         assertEquals("attending", n.getNodeName());         
         assertEquals("17004", n.getChildNodes().item(0).getTextContent());
         
         n = clist.item(1);
         assertEquals("unsure", n.getNodeName());         
         assertEquals("17002", n.getChildNodes().item(0).getTextContent());
         
         n = clist.item(2);
         assertEquals("declined", n.getNodeName());         
         assertEquals("17003", n.getChildNodes().item(0).getTextContent());
         
         n = clist.item(3);
         assertEquals("not_replied", n.getNodeName());         
         assertEquals("17001", n.getChildNodes().item(0).getTextContent());
         
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONObject obj = (JSONObject) resp;
         
         JSONArray arr = (JSONArray) obj.get("attending");
         assertEquals("17004", arr.get(0));         
         arr = (JSONArray) obj.get("unsure");
         assertEquals("17002", arr.get(0));
         arr = (JSONArray) obj.get("declined");
         assertEquals("17003", arr.get(0));
         arr = (JSONArray) obj.get("not_replied");
         assertEquals("17001", arr.get(0));
      }
          
      m_client.setDebug(false);
   }
   
   

}