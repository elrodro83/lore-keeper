package ar.com.lorekeeper.server.dao;

import java.util.Comparator;
import java.util.List;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import ar.com.lorekeeper.server.bean.Event;
import ar.com.lorekeeper.server.bean.PersistentObject;
import ar.com.lorekeeper.server.bean.Place;

import com.db4o.query.Predicate;

@Repository("eventDAO")
public class EventDao implements Dao {

	@Autowired
	private Db4oWrapper dbWrapper;

	public void save(final Event event) {
		dbWrapper.getDb().store(event);
	}

	public List<Event> getEventsForPlace(final Place where) {
		return dbWrapper.getDb().query(new Predicate<Event>() {
			@Override
			public boolean match(final Event ev) {
				return ev.happenedIn(where);
			}
		}, new Comparator<Event>() {
			@Override
			public int compare(final Event o1, final Event o2) {
				return o1.getWhenFrom().compareTo(o2.getWhenFrom());
			}
		});
	}

	@Override
	public PersistentObject find(final Object id) {
		return dbWrapper.getDb().query(new Predicate<Event>() {
			@Override
			public boolean match(final Event event) {
				return event.getId().equals(id);
			}
		}).next();
	}
}
