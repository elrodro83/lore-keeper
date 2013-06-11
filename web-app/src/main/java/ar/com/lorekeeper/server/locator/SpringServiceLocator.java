package ar.com.lorekeeper.server.locator;

import ar.com.lorekeeper.server.service.SpringWrapper;

import com.google.web.bindery.requestfactory.shared.ServiceLocator;

public class SpringServiceLocator implements ServiceLocator {

	@Override
	public Object getInstance(final Class<?> clazz) {
		return SpringWrapper.getInstance().getContext().getBean(clazz);
	}

}
