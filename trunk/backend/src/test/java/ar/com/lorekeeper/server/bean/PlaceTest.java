package ar.com.lorekeeper.server.bean;

import static org.junit.Assert.assertEquals;
import static org.junit.Assert.assertNotSame;
import static org.junit.Assert.assertSame;

import java.util.Date;
import java.util.List;

import org.junit.Test;
import org.junit.runner.RunWith;
import org.springframework.beans.factory.annotation.Autowired;
import org.springframework.test.context.ContextConfiguration;
import org.springframework.test.context.junit4.SpringJUnit4ClassRunner;

import ar.com.lorekeeper.server.dao.EventDao;
import ar.com.lorekeeper.server.dao.PlaceDao;

@RunWith(SpringJUnit4ClassRunner.class)
@ContextConfiguration(locations = { "classpath:applicationContext.xml" })
public class PlaceTest {

	@Autowired
	private EventDao eventDao;

	@Autowired
	private PlaceDao placeDao;

	@Test
	// @Transactional
	public void placesHierarchy() {
		Place building = new Place();
		building.setName("Building");
		placeDao.save(building);

		Place city = new Place();
		city.setName("City");
		city.addChild(building);
		placeDao.save(city);

		Place kingdom = new Place();
		kingdom.setName("Kingdom");
		kingdom.addChild(city);
		placeDao.save(kingdom);

		assertNotSame(building, city);
		assertNotSame(building, kingdom);
		assertNotSame(city, kingdom);
	}

	@Test
	// @Transactional
	public void events() {
		Place building = new Place();
		building.setName("Building");
		placeDao.save(building);

		Event buildingEvent = new Event();
		buildingEvent.setWhat("Construction");
		buildingEvent.setWhere(building);
		Date constructionDate = new Date(1000);
		buildingEvent.setWhenFrom(constructionDate);
		buildingEvent.setWhenTo(constructionDate);
		eventDao.save(buildingEvent);
		
		Place city = new Place();
		city.setName("City");
		city.addChild(building);
		placeDao.save(city);

		Event cityEvent = new Event();
		cityEvent.setWhat("Foundation");
		cityEvent.setWhere(city);
		Date foundationDate = new Date(500);
		cityEvent.setWhenFrom(foundationDate);
		cityEvent.setWhenTo(foundationDate);
		eventDao.save(cityEvent);
		
		Place kingdom = new Place();
		kingdom.setName("Kingdom");
		kingdom.addChild(city);
		placeDao.save(kingdom);

		List<Event> kingdomEvents = eventDao.getEventsForPlace(kingdom);

		assertEquals(2, kingdomEvents.size());
		assertEquals(cityEvent, kingdomEvents.get(0));
		assertEquals(buildingEvent, kingdomEvents.get(1));

		assertSame(cityEvent, kingdomEvents.get(0));
		assertSame(buildingEvent, kingdomEvents.get(1));
	}
}
