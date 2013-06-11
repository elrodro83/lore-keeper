package ar.com.lorekeeper.server.dao;

import ar.com.lorekeeper.server.bean.PersistentObject;

public interface Dao {

	PersistentObject find(Object id);

}
