package ar.com.lorekeeper.server.dao;

import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.stereotype.Repository;

import ar.com.lorekeeper.server.bean.Place;

@Repository("placeDAO")
public class PlaceDao {

	@Autowired
	private Db4oWrapper dbWrapper;

	public void save(Place place) {
		dbWrapper.getDb().store(place);
	}


}
