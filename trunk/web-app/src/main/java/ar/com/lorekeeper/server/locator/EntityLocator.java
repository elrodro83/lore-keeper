package ar.com.lorekeeper.server.locator;

import ar.com.lorekeeper.server.bean.PersistentObject;
import ar.com.lorekeeper.server.dao.Dao;
import ar.com.lorekeeper.server.service.SpringWrapper;

import com.google.web.bindery.requestfactory.shared.Locator;

public class EntityLocator extends Locator<PersistentObject, Object> {

	@Override
	public PersistentObject create(final Class<? extends PersistentObject> clazz) {
		try {
			return clazz.newInstance();
		} catch (final InstantiationException e) {
			throw new RuntimeException(e);
		} catch (final IllegalAccessException e) {
			throw new RuntimeException(e);
		}
	}

	@Override
	public PersistentObject find(final Class<? extends PersistentObject> clazz,
			final Object id) {
		final String simpleClazzName = clazz.getSimpleName();
		final Dao dao = SpringWrapper
				.getInstance()
				.getContext()
				.getBean(
						simpleClazzName.substring(0, 1).toLowerCase()
								+ simpleClazzName.substring(1) + "DAO",
						Dao.class);

		return dao.find(id);
	}

	@Override
	public Class<PersistentObject> getDomainType() {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public Object getId(final PersistentObject domainObject) {
		return domainObject.getId();
	}

	@Override
	public Class<Object> getIdType() {
		return Object.class;
	}

	@Override
	public Object getVersion(final PersistentObject domainObject) {
		return 0;
	}

}
