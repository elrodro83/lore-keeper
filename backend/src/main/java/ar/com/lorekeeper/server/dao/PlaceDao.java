package ar.com.lorekeeper.server.dao;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import ar.com.lorekeeper.server.bean.PersistentObject;
import ar.com.lorekeeper.server.bean.Place;

import com.db4o.ObjectSet;
import com.db4o.query.Predicate;

@Repository("placeDAO")
public class PlaceDao implements Dao {

	@Autowired
	private Db4oWrapper dbWrapper;

	public void save(final Place place) {
		dbWrapper.getDb().store(place);
	}

	@Override
	public PersistentObject find(final Object id) {
		final ObjectSet<Place> query = dbWrapper.getDb().query(
				new Predicate<Place>() {
					@Override
					public boolean match(final Place place) {
						return place.getId().equals(id);
					}
				});

		if (query.isEmpty()) {
			return null;
		} else {
			return query.next();
		}
	}

}
