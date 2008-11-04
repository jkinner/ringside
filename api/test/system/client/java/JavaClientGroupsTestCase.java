
import java.util.*;
import java.net.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientGroupsTestCase extends BaseJavaClientTestCase
{  
   public void testGroupsGet() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.groups_get(null, null);
      
      String[] flds = new String[] {"gid","name","nid","description","group_type","group_subtype",
                                    "recent_news","creator","office","website","email"};
      
      Map<String,String> valMap = extractObjectFields(resp, flds, 0);
      
      assertEquals("17800",valMap.get("gid"));
      assertEquals("group 1", valMap.get("name"));
      assertEquals("0", valMap.get("nid"));
      assertEquals("this is group 1", valMap.get("description"));
      assertEquals("Awesome Group", valMap.get("group_type"));
      assertEquals("Awesome sub-group", valMap.get("group_subtype"));
      assertEquals("No news is good news", valMap.get("recent_news"));
      assertEquals("17001", valMap.get("creator"));
      assertEquals("Suite 55", valMap.get("office"));
      assertEquals("http://www.nowhere.com", valMap.get("website"));
      assertEquals("nobody@nowhere.com", valMap.get("email"));
      
      String[] flds2 = new String[] {"street","city","state","country"};
      valMap = extractObjectFields(resp, flds2, 0, "venue");
      assertEquals("123 4th St.", valMap.get("street"));
      assertEquals("Nowherapolis", valMap.get("city"));
      assertEquals("ZZ", valMap.get("state"));
      assertEquals("France", valMap.get("country"));
      
      String[] flds3 = new String[] {"gid","name","nid","creator"};
      valMap = extractObjectFields(resp, flds3, 1);
      
      assertEquals("17801",valMap.get("gid"));
      assertEquals("group 2", valMap.get("name"));
      assertEquals("0", valMap.get("nid"));
      assertEquals("17002", valMap.get("creator"));
      
      m_client.setDebug(false);
   }
   
   public void testGroupsGetMembers() throws Exception
   {
      //m_client.setDebug(true);
      Object resp = m_client.groups_getMembers(17800);
      
      List<String> arr = extractArray(resp, "members");
      assertTrue(arr.contains("17001"));
      assertTrue(arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "admins");
      assertTrue(arr.contains("17001"));
      assertTrue(!arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "officers");
      assertTrue(!arr.contains("17001"));
      assertTrue(arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "not_replied");
      assertTrue(!arr.contains("17001"));
      assertTrue(!arr.contains("17002"));
      assertTrue(arr.contains("17003"));
      assertTrue(arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      resp = m_client.groups_getMembers(17801);
      
      arr = extractArray(resp, "members");
      assertTrue(arr.contains("17001"));
      assertTrue(arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "admins");
      assertTrue(!arr.contains("17001"));
      assertTrue(arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "officers");
      assertTrue(arr.contains("17001"));
      assertTrue(!arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      arr = extractArray(resp, "not_replied");
      assertTrue(!arr.contains("17001"));
      assertTrue(!arr.contains("17002"));
      assertTrue(!arr.contains("17003"));
      assertTrue(!arr.contains("17004"));
      assertTrue(!arr.contains("17005"));
      
      m_client.setDebug(false);
   }
   
   public static List<String> extractArray(Object resp, String fieldName)
   {  
      List<String> arr = new ArrayList<String>();
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;         
         Node p = getChildNodeByName(xresp.getDocumentElement(), fieldName);
         if (p == null) fail("Couldn't find sub-field '" + fieldName + "'");
         NodeList clist = p.getChildNodes();
         for (int k = 0; k < clist.getLength(); k++) {
            arr.add(clist.item(k).getTextContent());
         }         
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONObject obj = (JSONObject) resp;
         JSONArray arr2 = (JSONArray) obj.get(fieldName);
         if (arr2 == null) fail("Couldn't find sub-field '" + fieldName + "'");
         for (int k = 0; k < arr2.size(); k++) {
            arr.add(arr2.get(k).toString());
         }
      }
      
      return arr;
   }

   
}