package ar.com.lorekeeper.shared.bean;

import ar.com.lorekeeper.server.bean.SearchResult;

import com.google.web.bindery.requestfactory.shared.ProxyFor;
import com.google.web.bindery.requestfactory.shared.ValueProxy;

@ProxyFor(value = SearchResult.class)
public interface SearchResultProxy extends ValueProxy {

	String getType();

	Long getId();

	String getName();

	String getDescription();

}
