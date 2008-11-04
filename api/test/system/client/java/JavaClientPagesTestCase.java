
import java.util.*;

import junit.framework.*;
import static junit.framework.Assert.*;
import org.w3c.dom.*;
import org.json.simple.*;
import com.facebook.api.*;

public class JavaClientPagesTestCase extends BaseJavaClientTestCase
{  
   public void testPagesGetInfo() throws Exception
   {
      //m_client.setDebug(true);
      List<Long> pids = new ArrayList<Long>();
      pids.add((long) 17700);
      
      EnumSet<PageProfileField> eset = EnumSet.of(PageProfileField.PAGE_ID,
            PageProfileField.FOUNDED, PageProfileField.MISSION, PageProfileField.WEBSITE,
            PageProfileField.COMPANY_OVERVIEW,PageProfileField.PRODUCTS,PageProfileField.NAME,
            PageProfileField.TYPE,PageProfileField.PIC_BIG,PageProfileField.PIC_SMALL,
            PageProfileField.PIC_SQUARE,PageProfileField.PIC_LARGE,PageProfileField.PIC);
      
      Object resp = m_client.pages_getInfo(pids, eset);
      
      String[] vals = new String[] {"page_id","founded","mission","website",
            "company_overview", "products","name","type","pic_big","pic_small","pic_square",
            "pic_large","pic"};
      
      Map<String,String> valMap = new HashMap<String,String>();
      
      if ("XML".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         Document xresp = (Document) resp;
         NodeList clist = xresp.getDocumentElement().getChildNodes();
         assertEquals(1, clist.getLength());
         Node p = clist.item(0);
         assertEquals("page", p.getNodeName());
       
         for (int k = 0; k < vals.length; k++) {
            Node n = getChildNodeByName(p, vals[k]);
            if (n == null) fail("Couldn't retreive child node '" + vals[k] + "'");
            valMap.put(vals[k], n.getTextContent());
         }         
      } else if ("JSON".equals(JavaClientTestRunner.RESPONSE_TYPE)) {
         JSONArray arr = (JSONArray) resp;
         JSONObject obj = (JSONObject) arr.get(0);
         
         for (int k = 0; k < vals.length; k++) {
            String val = obj.get(vals[k]).toString();
            if (val == null) fail("Couldn't retreive child node '" + val + "'");
            valMap.put(vals[k], val);
         }
      }
      
      
      assertEquals("17700", valMap.get("page_id"));
      assertEquals("Early last year", valMap.get("founded"));
      assertEquals("A long mission with a nice vision", valMap.get("mission"));
      assertEquals("http://mycrazystore.com/", valMap.get("website"));
      assertEquals("Crazy guys with crazy store", valMap.get("company_overview"));
      assertEquals("A b C and D", valMap.get("products").trim());      
      assertEquals("CRAZYONLINE", valMap.get("name"));
      assertEquals("ONLINE_STORE", valMap.get("type"));
      assertEquals("http://www.picit.com/image1.jpg", valMap.get("pic_big"));
      assertEquals("http://www.picit.com/image1.jpg", valMap.get("pic_small"));
      assertEquals("http://www.picit.com/image1.jpg", valMap.get("pic_square"));
      assertEquals("http://www.picit.com/image1.jpg", valMap.get("pic_large"));
      assertEquals("http://www.picit.com/image1.jpg", valMap.get("pic"));
      
      m_client.setDebug(false);
   }
   
   public void testPagesIsFan() throws Exception
   {
      //m_client.setDebug(true);
      assertTrue(m_client.pages_isFan((long) 17700, 17002));
            
      boolean failed = false;
      try {
         //can't find fans that aren't friends of 17001
         m_client.pages_isFan((long) 17700, 17005);
      } catch (Exception e) {
         failed = true;
      }
      assertTrue(failed);
   }
   
   public void testPagesIsAdmin() throws Exception
   {
      assertTrue(m_client.pages_isAdmin((long) 17700));
      
      initClient("17002");
      assertTrue(!m_client.pages_isAdmin((long) 17700));
   }
   
   public void testPagesIsAppAdded() throws Exception
   {
      assertTrue(m_client.pages_isAppAdded((long) 17700));
   }
   
}