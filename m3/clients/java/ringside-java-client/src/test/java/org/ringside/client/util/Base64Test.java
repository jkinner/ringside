package org.ringside.client.util;

import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.OutputStream;
import java.util.Arrays;
import java.util.Random;

import org.testng.annotations.Test;

@Test
public class Base64Test
{
    private static final long SEED = 12345678;
    private static Random s_random = new Random(SEED);
    
    private byte[] createData(int length) throws Exception
    {
        byte[] bytes = new byte[length];
        s_random.nextBytes(bytes);
        return bytes;
    }

    private void runStreamTestNoOptions(int length) throws Exception
    {
    	runStreamTest(length, Base64.NO_OPTIONS);
    }

    private void runStreamTestGZip(int length) throws Exception
    {
    	runStreamTest(length, Base64.GZIP);
    }
    
    private void runStreamTest(int length, int options) throws Exception
    {
        byte[] data = createData(length);
        ByteArrayOutputStream out_bytes = new ByteArrayOutputStream();
        OutputStream out = new Base64.OutputStream(out_bytes);
        out.write(data);
        out.close();
        byte[] encoded = out_bytes.toByteArray();
        byte[] decoded = Base64.decode(encoded, 0, encoded.length, options);
        assert Arrays.equals(data, decoded);
        
        Base64.InputStream in = new Base64.InputStream(new ByteArrayInputStream(encoded));
        out_bytes = new ByteArrayOutputStream();
        byte[] buffer = new byte[3];
        for (int n = in.read(buffer); n > 0; n = in.read(buffer)) {
            out_bytes.write(buffer, 0, n);
        }
        out_bytes.close();
        in.close();
        decoded = out_bytes.toByteArray();
        assert Arrays.equals(data, decoded);
    }
    
    public void testStreamsNoOptions() throws Exception
    {
        for (int i = 0; i < 100; ++i) {
            runStreamTestNoOptions(i);
        }
        for (int i = 100; i < 2000; i += 250) {
            runStreamTestNoOptions(i);
        }
        for (int i = 2000; i < 80000; i += 1000) {
            runStreamTestNoOptions(i);
        }
    }

    public void testStreamsGzip() throws Exception
    {
    	for (int i = 0; i < 100; ++i) {
    		runStreamTestGZip(i);
    	}
    	for (int i = 100; i < 2000; i += 250) {
    		runStreamTestGZip(i);
    	}
    	for (int i = 2000; i < 80000; i += 1000) {
    		runStreamTestGZip(i);
    	}
    }
}
