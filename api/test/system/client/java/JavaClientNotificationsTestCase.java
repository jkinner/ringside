import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientNotificationsTestCase extends BaseJavaClientTestCase
{  
   public void testNotificationsGet() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.notifications_get();
      
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(6, clist.getLength());
         
         Node m = clist.item(0);
         assertEquals("messages", m.getNodeName());
         Node n = getChildNodeByName(m, "unread");
         assertEquals("0", n.getTextContent());
         n = getChildNodeByName(m, "most_recent");
         assertEquals("0", n.getTextContent());
         
         m = clist.item(1);
         assertEquals("pokes", m.getNodeName());
         n = getChildNodeByName(m, "unread");
         assertEquals("2", n.getTextContent());
         n = getChildNodeByName(m, "most_recent");
         assertEquals("17003", n.getTextContent());
         
         m = clist.item(2);
         assertEquals("shares", m.getNodeName());
         n = getChildNodeByName(m, "unread");
         assertEquals("2", n.getTextContent());
         n = getChildNodeByName(m, "most_recent");
         assertEquals("17202", n.getTextContent());
         
         m = clist.item(5);
         assertEquals("event_invites", m.getNodeName());
         n = getChildNodeByName(m, "eid");
         assertEquals("17400", n.getTextContent());
         
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {
         JSONObject obj = (JSONObject) resp;
         
         JSONObject s = (JSONObject) obj.get("shares"); 
         assertEquals("2", s.get("unread").toString());
         assertEquals("17202", s.get("most_recent"));
         
         s = (JSONObject) obj.get("messages"); 
         assertEquals("0", s.get("unread").toString());
         assertEquals("0", s.get("most_recent").toString());
         
         s = (JSONObject) obj.get("pokes"); 
         assertEquals("2", s.get("unread").toString());
         assertEquals("17003", s.get("most_recent").toString());
         
         JSONArray arr = (JSONArray) obj.get("event_invites"); 
         assertEquals("17400", arr.get(0));
      }
      m_client.setDebug(false);
   }
   
   public void testNotificationsSend() throws Exception
   {
      List<Integer> uids = new ArrayList<Integer>();
      uids.add(17002);
      uids.add(17003);
      m_client.notifications_send(uids, "goats!");      
      
      initClient("17002");
      Object resp = m_client.notifications_get();      
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(6, clist.getLength());
         
         Node m = clist.item(0);
         assertEquals("messages", m.getNodeName());
         Node n = getChildNodeByName(m, "unread");
         assertEquals("1", n.getTextContent());          
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {
         JSONObject obj = (JSONObject) resp;         
         JSONObject s = (JSONObject) obj.get("messages"); 
         assertEquals("1", s.get("unread").toString());
      }
      
      initClient("17003");
      resp = m_client.notifications_get();      
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(6, clist.getLength());
         
         Node m = clist.item(0);
         assertEquals("messages", m.getNodeName());
         Node n = getChildNodeByName(m, "unread");
         assertEquals("1", n.getTextContent());          
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {
         JSONObject obj = (JSONObject) resp;         
         JSONObject s = (JSONObject) obj.get("messages"); 
         assertEquals("1", s.get("unread").toString());
      }            
   }
   
   public void testNotificationsSendEmail() throws Exception
   {
      //TODO: this test case doesn't seem to be correct...
      //m_client.setDebug(true);
      List<Integer> uids = new ArrayList<Integer>();
      uids.add(17003);
      m_client.notifications_sendEmail(uids, "no subject", "", "this is the message body");
      
      initClient("17003");
      Object resp = m_client.notifications_get();      
      if (JavaClientTestRunner.RESPONSE_TYPE.equals("XML")) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(6, clist.getLength());
         
         Node m = clist.item(0);
         assertEquals("messages", m.getNodeName());
         Node n = getChildNodeByName(m, "unread");
         assertEquals("1", n.getTextContent());          
      } else if (JavaClientTestRunner.RESPONSE_TYPE.equals("JSON")) {
         JSONObject obj = (JSONObject) resp;         
         JSONObject s = (JSONObject) obj.get("messages"); 
         assertEquals("1", s.get("unread").toString());
      }
      
      m_client.setDebug(false);
   }
   
}