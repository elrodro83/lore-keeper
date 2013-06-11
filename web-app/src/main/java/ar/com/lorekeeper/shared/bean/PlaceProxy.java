package ar.com.lorekeeper.shared.bean;

import ar.com.lorekeeper.server.bean.Place;
import ar.com.lorekeeper.server.locator.EntityLocator;

import com.google.web.bindery.requestfactory.shared.EntityProxy;
import com.google.web.bindery.requestfactory.shared.ProxyFor;

@ProxyFor(value = Place.class, locator = EntityLocator.class)
public interface PlaceProxy extends EntityProxy {
	String getName();

	void setName(String name);

	String getDescription();

	void setDescription(String description);

	PlaceProxy getParent();

	void setParent(PlaceProxy parent);

}
