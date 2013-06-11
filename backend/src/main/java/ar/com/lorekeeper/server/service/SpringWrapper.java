package ar.com.lorekeeper.server.service;

import org.springframework.context.ApplicationContext;
import org.springframework.context.support.ClassPathXmlApplicationContext;

public class SpringWrapper {

	private static final SpringWrapper INSTANCE = new SpringWrapper();

	public static SpringWrapper getInstance() {
		return INSTANCE;
	}

	private SpringWrapper() {
		// Nothing to do
	}

	private ApplicationContext context;

	public ApplicationContext getContext() {
		if (context == null) {
			initContext();
		}
		return context;
	}

	private synchronized void initContext() {
		if (context == null) {
			context = new ClassPathXmlApplicationContext(
					"/applicationContext.xml");
		}
	}
}
