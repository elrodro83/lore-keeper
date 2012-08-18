package ar.com.lorekeeper.shared.bean;

import ar.com.lorekeeper.server.bean.Place;

import com.google.web.bindery.requestfactory.shared.EntityProxy;
import com.google.web.bindery.requestfactory.shared.ProxyFor;

@ProxyFor(Place.class)
public interface PlaceProxy extends EntityProxy {
	String getName();

	void setName(String name);

	String getDescription();

	void setDescription(String description);

	PlaceProxy getParent();

	void setParent(PlaceProxy parent);

}
