package ar.com.lorekeeper.server.dao;

import java.util.Comparator;
import java.util.List;

import javax.annotation.PostConstruct;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import ar.com.lorekeeper.server.bean.Event;
import ar.com.lorekeeper.server.bean.Place;

import com.db4o.Db4o;
import com.db4o.ObjectContainer;
import com.db4o.query.Predicate;

@Repository("eventDAO")
public class EventDao {

	@Autowired
	private Db4oWrapper dbWrapper;

	public void save(Event event) {
		dbWrapper.getDb().store(event);
	}

	public List<Event> getEventsForPlace(final Place where) {
		return dbWrapper.getDb().query(new Predicate<Event>() {
			@Override
			public boolean match(Event ev) {
				return ev.happenedIn(where);
			}
		}, new Comparator<Event>() {
			@Override
			public int compare(Event o1, Event o2) {
				return o1.getWhenFrom().compareTo(o2.getWhenFrom());
			}
		});
	}
}
